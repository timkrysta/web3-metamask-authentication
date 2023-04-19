function isMetaMaskInstalled() {
    //Have to check the ethereum binding on the window object to see if it's installed
    const { ethereum } = window;
    if (ethereum && ethereum.isMetaMask)
    {
        return true;
    } else {
        return false;
    }
    //spinner_on_white.style.display = "none";
}



// This is not so perfect cuz dont return what currecy balace we are taking
// BUT if address starts with 0x you can be sure that this is ethereum address
async function getMetaMaskBalance(account) {
    const web3 = new Web3(window.ethereum);
    // the value is returned in wei so  1,000,000,000,000,000,000 wei = 1 eth   |   Ether	10*18 wei
    let result_in_wei = await web3.eth.getBalance(account); // returns str
    //console.log(result_in_wei);

    // takes first arg (number) only as string and second (unit) defaults to 'ether'
    let result_in_eth = web3.utils.fromWei(result_in_wei, 'ether'); // returns str
    //console.log(result_in_eth);

    return result_in_eth;
    // convert str to number
    try {
        return Number(result_in_eth);
    } catch (e) {
        return parseFloat(result_in_eth);
    }
}



async function getAccounts()
{
    // NOTE NOT SURE we use eth_accounts because it returns a list of addresses owned by us.

    // Returns a hexadecimal string representing the user's "currently selected" address.
    // The "currently selected" address is the first item in the array returned by eth_accounts
    try {
        const accounts = await ethereum.request({ method: 'eth_accounts' });
        return accounts[0];
    } catch (error) {
        // Some unexpected error.
        // For backwards compatibility reasons, if no accounts are available,
        // eth_accounts will return an empty array.
        console.error(getErrorResponse(error, "getAccounts"));
    }

    //We take the first address in the array of addresses and display it
}


async function getAccounts()
{
    //we use eth_accounts because it returns a list of addresses owned by us.

    try {
        let accounts = await ethereum.request({ method: 'eth_accounts' });
    } catch (error) {
        // Some unexpected error.
        // For backwards compatibility reasons, if no accounts are available,
        // eth_accounts will return an empty array.
        console.error(getErrorResponse(error, "getAccounts"));
    }
    //We take the first address in the array of addresses and display it

    //showAccount.innerHTML = accounts[0] || 'Not able to get accounts';

    // Returns a hexadecimal string representing the user's "currently selected" address.
    // The "currently selected" address is the first item in the array returned by eth_accounts
}

/* async function getAccounts()
{
    // NOTE NOT SURE we use eth_accounts because it returns a list of addresses owned by us.

    // Returns a hexadecimal string representing the user's "currently selected" address.
    // The "currently selected" address is the first item in the array returned by eth_accounts
    try {
        // This line gets all account addresses user selected through checkbox while connecting
        const accounts = await ethereum.request({ method: 'eth_accounts' });
        return accounts;
    } catch (error) {
        // Some unexpected error.
        // For backwards compatibility reasons, if no accounts are available,
        // eth_accounts will return an empty array.
        console.error(getErrorResponse(error, "getAccounts"));
    }
    //We take the first address in the array of addresses and display it
}
 */




// Optional error message
// console.error(error);
// console.error(error.message);

const getErrorResponse = (error, functionName) => {
    const errorText = typeof error === "string" ? error : error.message;
    const res = {
        /* eslint-disable-nextline i18next/no-literal-string */
        message: `function_name: ${functionName}(): ${errorText}`,
    };
    const ABORTED = "aborted - The request was rejected by the user";
    const EXCEPTION = "exception";

    // these both were added by me
    const INVALID_PARAMETERS = "The parameters were invalid";
    const INTERNAL_ERROR = "Internal error";

    const UNKOWN = "unknown error type";
    // WARNING: consider not displaying extended error response like this
    if (error.code) {
        res.code = error.code;
        switch (error.code) {
            case 4001:
                res.errorType = ABORTED;
                break;
            case -32016:
                res.errorType = EXCEPTION;
                break;
            case -32602:
                res.errorType = INVALID_PARAMETERS;
                break;
            case -32603:
                res.errorType = INTERNAL_ERROR;
                break;
            default:
                res.errorType = UNKOWN;
        }

    }
    return { error: res };
};
// NOTE: usage: return [...] or getErrorResponse(error, "signMessage");








/**********************************************************/
/* 2x Functions for Handle chain (network) and chainChanged (per EIP-1193) Detect which Ethereum network the user is connected to */
/**********************************************************/
async function getChainId () {
    try {
        const chainId = await ethereum.request({ method: 'eth_chainId' }); // chainId is still Promise
        // Handle the result
        let result = await chainId; // now result is string

        //console.log(result);
        return result;
    }
    catch (error) {
        console.error(getErrorResponse(error, "getChainId"));
    }
}


// When user will change chain (network) page will reload
function handleChainChanged(_chainId) {
    // MetaMask recommend reloading the page on chainChanged, unless you must do otherwise

    console.log(JSON.stringify(_chainId));
    window.location.reload();
}










function startApp() {
    // MetaMask: The event 'networkChanged' is deprecated and may be removed in the future. Use 'chainChanged' instead.
    ethereum.on('chainChanged', handleChainChanged);


    /*****************************************/
    /* Handle on accountsChanged */
    /*****************************************/
    // If you'd like to be notified when the address changes, we have an event you can subscribe to:
    ethereum.on('accountsChanged', (accounts) => {
        // When user deny access to accounts OR lock metamask or disconnect from last account it will return []

        // If user has locked/logout from MetaMask, this resets the accounts array to empty

        // this is faster than above getAccount() func

        // Time to reload your interface with accounts[0]!

        // Handle the new accounts, or lack thereof.
        // "accounts" will always be an array, but it can be empty.

        //it runs with empty argument when you disconnect your metamask account
        // if user picked more than use account checkbox while connecting and he switch account this will run
        // same if he connected 2+ accounts and he will disconnect manualy from one then it will switch autmaticaly to another
        // The only way to detect if user disconnects from last account is that below console.log will return empty array!!!!
        //console.log(JSON.stringify(accounts));

        // Note that this event is emitted on page load.
        // If the array of accounts is non-empty, you're already connected.

        // WARNING: THINK OF ADDING BELOW LINE
        //ethereum.on('accountsChanged', handleAccountsChanged);
        handleAccountsChanged(accounts); // I dont know if it is a good idea to run it here
    });
    // If the first account in the returned array isn't the account you expected, you should notify the user!
    // In the future, the accounts array may contain more than one account.
    // However, the first account in the array will continue to be considered as the user's "selected" account.
}






/*****************************************/
/* Alert accounts on click */
/*****************************************/

// In our UI, connected/disconnected refers to whether a dapp has access to one or more of the user's accounts.



