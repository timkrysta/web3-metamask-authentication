
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="eth_address" content="{{ $eth_address }}" />
    <title>Home</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">

    @include('components.web3_js')

    <script src="{{ asset('ajax.js') }}"></script>
    <script src="{{ asset('metamask-utils.js') }}"></script>
</head>

<body>
    <div class="container">
        <div class="row">
            <h1>You are logged in</h1>
            <div class="col-12 text-center mt-3">
                <div class="dropdown" style="float:right;">
                    <a href="{{ route('logout') }}" class="btn btn-secondary">
                        Logout
                    </a>
                </div>
            </div>
            <div class="col-12">
                <div>ETH address: <div id="ethAddress"></div></div>
                <div>Account balance: <div id="account_balance"></div></div>
                <div>Chain ID: <div id="chain_id"></div></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>

    <script>
        async function checkIfEverythingIsCorrect()
        {
            if (! isMetaMaskInstalled())
            {
                // User after login deleted or disabled metamask extension
                logout('Metamask is not found. Turn it on or install it.');
            }

            const accounts = await ethereum.request({ method: 'eth_accounts' });
            const ethAccount = accounts[0];

            if (ethAccount === "undefined") {
                // User must have locked metamask or disconnected wallet manually
                logout();
            }

            if (ethAccount != document.querySelector('meta[name="eth_address"]').content) {
                // User must have locked metamask or disconnected wallet manually while having one or multiple addresses (accounts) connected (session address does not match with current from wallet)
                logout('Your security token has changed.');
            }
        }
        function handleAccountsChanged(accounts)
        {
            if (accounts.length === 0) {
                // MetaMask is locked or a user has disconnected manually from the last account
                logout();
            }
            else if (accounts[0] !== currentAccount) {
                currentAccount = accounts[0];

                if (currentAccount != null) {
                    // This will be true if user connected manually with new account
                }
            }
            checkIfEverythingIsCorrect();
        }
        function logout(message = '')
        {
            console.log('logout:', message);
            ajax({
                url: "{{ route('logout') }}",
                type: 'GET',
                async: false,
                success: (xhr, data) => {
                    //window.location.refresh();
                },
            });
            //window.location = "{{ route('logout') }}";
        }
        async function displayInfo()
        {
            const accounts = await ethereum.request({ method: 'eth_accounts' });
            let chain_id = await getChainId();
            let account_balance = await getMetaMaskBalance(document.querySelector('meta[name="eth_address"]').content);

            document.getElementById('ethAddress').innerHTML = accounts[0];
            document.getElementById('chain_id').innerHTML = chain_id;
            document.getElementById('account_balance').innerHTML = account_balance + ' ETH';
        };
    </script>

    <script>
        let currentAccount = null;
        displayInfo();
        checkIfEverythingIsCorrect();
        startApp();
    </script>
</body>
</html>
