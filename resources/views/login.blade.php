<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    {{-- Challenge is to sign a nonce (random string) provided by server. --}}
    <meta name="nonce" content="{{ $nonce }}" />
    <title>Connect with MetaMask</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
    @include('components.web3_js')

    <style>
        .center_verticaly {
            display:inline-block;
            vertical-align:middle;
        }
        .radius_plus_padding {
            border-radius: .5rem;
            padding:.85rem;
        }
    </style>

    <script src="{{ asset('ajax.js') }}"></script>
    <script src="{{ asset('metamask-utils.js') }}"></script>
</head>

<body>
    @include('components.loading_spinner')

    <div class="container" style="height:100vh;">
        {{-- <p>If you have already MetaMask installed reload page</p>
        <p>
            If you logged out from one account and you want to now switch to other account,
            unless you added 2 checkboxes while connecting so you should be loggedin on the second,
            first you need to manually disconnect in metamask extension click disconnect from website
        </p>
        <p>
            Also if you will have some problems with changing account always check metamask extesion -
            click () icon in your browser and disconnect from this account you dont want.
            Tutorial: <a href="#">here</a>
        </p> --}}
        <div class="row" id="login_wrapper" style="height:100%;">
            <div class="col-12">
                <div style="width: 100%;height: 100%;display: flex;align-items: center;justify-content: center;">
                    <button id="connectButton" class="btn btn-primary btn-lg">
                        Connect with MetaMask
                    </button>
                    <div>
                        <div style="display:none;">
                            Status:
                            <div id="status"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-install-metamask-overlay/>

        <x-initializing-metamask-modal/>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <script>
        // User needs to personal_sign to prove they own the wallet (public ETH address).
        async function signData()
        {
            const web3 = new Web3(window.ethereum);

            // Get all account addresses user selected through checkbox while connecting
            const accounts = await window.ethereum.request({ method: 'eth_accounts' });
            const current_account_with_correct_checksum = Web3.utils.toChecksumAddress(accounts[0]);

            try
            {
                // Alternative:
                // web3.eth.personal.sign(MESSAGE_TO_SIGN, current_account_with_correct_checksum)
                //    .then(async (signature) => {});
                let signature = await web3.eth.personal.sign(MESSAGE_TO_SIGN, current_account_with_correct_checksum); // you can pass the third parameter (password)

                // The goal is to extract from the sign the wallet address that signed the request. In this way, there's no way to fake it.
                const ethAddressThatSignedMessage = await web3.eth.personal.ecRecover(MESSAGE_TO_SIGN, signature);

                // ethAddressThatSignedMessage is lowercase, so we use Web3.utils.toChecksumAddress() that will convert an upper or lowercase Ethereum address to a checksum address.
                if (current_account_with_correct_checksum != Web3.utils.toChecksumAddress(ethAddressThatSignedMessage)) {
                    // console.log('Failed to verify the signer');
                    return;
                }
                // console.log('Successfully verified the signer');
                sendLoginRequest(current_account_with_correct_checksum, MESSAGE_TO_SIGN, signature);
                hideModal();
            }
            // If user will reject to sign the message
            catch (error) {
                hideModal();
                //console.error(getErrorResponse(error, "personal.sign"));
                // You are connected but you refused to sign message and we can not log you in - Reload the page and try again!
                window.location.reload();
            }
        }

        function handleAccountsChanged(accounts)
        {
            // MetaMask is locked or the user has not connected any accounts
            if (accounts.length === 0) return;

            if (accounts[0] !== currentAccount) {
                currentAccount = accounts[0];
                signData();
            }
        }

        // This is function from MetaMask documentation
        function connect()
        {
            disableConnectButton();

            ethereum
                .request({ method: "eth_requestAccounts", params: [ { eth_accounts: {} }] })
                .then((accounts) => {
                    connectButton.innerHTML = "Connected but not verified";
                    handleAccountsChanged(accounts);
                })
                .catch((error) => {
                    // Some unexpected error.
                    // For backwards compatibility reasons, if no accounts are available,
                    // eth_accounts will return an empty array.

                    enableConnectButton();

                    // EIP-1193 userRejectedRequest error
                    // If this happens, the user rejected the connection request.
                    if (error.code === 4001) {
                        status.innerHTML = "You refused to connect Metamask"; //status.innerHTML = error.message;
                    } else {
                        console.error(getErrorResponse(error, "connect"));
                    }
                });
        }

        function sendLoginRequest(ethAddress, message, signature)
        {
            ajax({
                url: "{{ route('authenticate') }}",
                type: 'POST',
                async: false,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                data: {
                    'ethAddress': ethAddress,
                    'message': message,
                    'signature': signature,
                },
                success: (xhr, data) => {
                    window.location = "{{ route('home') }}";
                },
            });
        }
        function handleEthereum()
        {
            if (isMetaMaskInstalled()) {
                // Access the decentralized web!

                // You should only attempt to request the user's accounts in response to user interaction, such as a button click. Otherwise, you popup-spam the user like it's 1999.
                connectButton.onclick = connect;

                startApp();
            } else {
                informUserToInstallMetaMask();
            }
            hideSpinner();
        }
        function informUserToInstallMetaMask()
        {
            connectButton.innerHTML = "Install MetaMask!";
            connectButton.onclick = () => {
                window.open('https://metamask.io', '_blank');
            };
            document.getElementById('install_metamask_overlay').style.display = "block";
            document.getElementById('login_wrapper').style.display = "none";
        }
        function disableConnectButton()
        {
            document.body.style.cursor = "progress";
            initializing_metamask_modal.show();
            connectButton.disabled = true;
            connectButton.innerHTML = "Connecting...";
        }
        function enableConnectButton()
        {
            document.body.style.cursor = "auto";
            initializing_metamask_modal.hide();
            connectButton.disabled = false;
            connectButton.innerHTML = "Connect with MetaMask";
        }
        function showSpinner()
        {
            spinner_on_white.style.display = "block";
        }
        function hideSpinner()
        {
            spinner_on_white.style.display = "none";
        }
        function hideModal()
        {
            document.body.style.cursor = "auto";
            initializing_metamask_modal.hide();
        }
    </script>

    <script>
        let currentAccount = null;
        let spinner_on_white = document.getElementById('spinner_on_white');
        let connectButton = document.getElementById('connectButton');
        let status = document.getElementById('status');
        let initializing_metamask_modal = new bootstrap.Modal(document.getElementById('initializing_metamask_modal'), {});
        const MESSAGE_TO_SIGN =
            "Hi there from YOUR APP NAME! Sign this message to prove you have access to this wallet and we’ll log you in. This won’t cost you any Ether." +
            "\n\n" +
            "🔒 For security purposes, here’s a unique message ID: " +
            document.querySelector('meta[name="nonce"]').content +
            "\n " +
            "Note: you don't have to remember or write it down.";

        showSpinner();

        // Below if/else is to reliably detect both the mobile and extension provider.
        if (window.ethereum) {
            handleEthereum();
        } else {
            // if metamask is not detected it will take 3sec to be sure if is not installed
            window.addEventListener("ethereum#initialized", handleEthereum, {once: true});

            // If the event is not dispatched by the end of the timeout, the user probably doesn't have MetaMask installed.
            setTimeout(handleEthereum, 3000); // 3 seconds
        }
    </script>
</body>
</html>
