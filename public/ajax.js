/**
 * Returns a new XMLHttpRequest object
 */
const createXHR = () => {
  if (typeof XMLHttpRequest !== 'undefined') {
    return new XMLHttpRequest();
  }
  const versions = [
    "MSXML2.XmlHttp.6.0",
    "MSXML2.XmlHttp.5.0",
    "MSXML2.XmlHttp.4.0",
    "MSXML2.XmlHttp.3.0",
    "MSXML2.XmlHttp.2.0",
    "Microsoft.XmlHttp"
  ];
  let xhr;
  for (let i = 0; i < versions.length; i++) {
    try {
      xhr = new ActiveXObject(versions[i]);
      break;
    } catch (e) {}
  }
  return xhr;
};


/**
  Sends an AJAX request with the specified options
  @param {object}   options              - The options for the AJAX request
  @param {string}   options.url          - The URL to which the request is sent
  @param {string}   [options.type=GET]   - The type of the request (GET or POST)
  @param {boolean}  [options.async=true] - Whether the request should be asynchronous or not
  @param {object}   [options.data]       - The data to send with the request
  @param {object}   [options.headers]    - Additional headers to send with the request
  @param {function} [options.beforeSend] - A function to be called before the request is sent
  @param {function} [options.progress]   - A function to be called during the progress of the request
  @param {function} [options.success]    - A function to be called when the request is successful
  @param {function} [options.failure]    - A function to be called when the request fails
  @param {function} [options.complete]   - A function to be called when the request is complete (regardless of success or failure)
  @param {function} [options.error]      - A function to be called when there is an error with the request
  @param {function} [options.abort]      - A function to be called when the request is aborted
  @param {number}   [options.timeout]    - The timeout for the request in milliseconds
  @param {function} [options.ontimeout]  - A function to be called when the request times out
*/
function ajax({
    url   = window.location.href,
    type  = 'GET',
    async = true,
    data,
    headers,
    beforeSend,
    progress,
    success,
    failure,
    complete,
    error,
    abort,
    timeout,
    ontimeout
  } = {})
{

  if (beforeSend && beforeSend() === false) {
      console.log('Aborting AJAX request');
      return;
  }

  const xhr = createXHR();
  xhr.withCredentials = true;

  if (progress) {
    xhr.onprogress = (e) => {
      // e.loaded           = how many bytes downloaded
      // e.lengthComputable = true if the server sent Content-Length header, if true we can find the size of the response
      // e.total            = total number of bytes (if lengthComputable is true)
      const total = e.lengthComputable ? e.total : 0;
      const loaded = e.loaded;
      progress(xhr, loaded, total);
    }
  }
  xhr.onreadystatechange = () => {
    if (xhr.readyState == 2) {
      // console.log("Header received");
    }
    else if (xhr.readyState == 3) {
      // console.log("Loading response");
    }
    else if (xhr.readyState == 4) {
      // console.log("Request Finished");
      if (xhr.status === 200) {
        if (success) success(xhr, xhr.responseText);
      }
      else {
        if (failure) failure(xhr, xhr.responseText);
      }
    }
  };

  if (complete) { xhr.onload  = () => complete(xhr); }
  if (error)    { xhr.onerror = () => error(xhr, xhr.statusText); }
  if (abort)    { xhr.onabort = () => abort(xhr); }

  if (timeout)   { xhr.timeout = timeout; }
  if (ontimeout) { xhr.ontimeout = () => ontimeout(xhr); }

  try {
    let query = [];
    for (const key in data) {
      query.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
    }

    type = type.toUpperCase();
    if (type == 'GET') {
      data = null;
      url  = url + (query.length ? '?' + query.join('&') : '');
    }
    else if (type == 'POST') {
      data = query.join('&');
    }

    xhr.open(type, url, async);

    if (type == 'POST') {
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    }

    for (const key in headers) {
      if (Object.hasOwnProperty.call(headers, key)) {
        xhr.setRequestHeader(key, headers[key]);
      }
    }

    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

    xhr.send(data);
  }
  catch (e) {
    console.log("Unable to connect to server");
  }
}
