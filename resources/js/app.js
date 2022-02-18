import cookies from 'browser-cookies';

(function(win) {
  const doc = win.document;
  const token = cookies.get('XSRF-TOKEN');

  [...doc.querySelectorAll('input[name="_token"]')].forEach(inp => {
    const xsrfInput = doc.createElement('input');

    xsrfInput.setAttribute('type', 'hidden');
    xsrfInput.setAttribute('name', '_xsrf_token');
    xsrfInput.setAttribute('value', token);

    const parent = inp.parentNode;

    parent.insertBefore(xsrfInput, inp);
    parent.removeChild(inp);
  });
})(global);
