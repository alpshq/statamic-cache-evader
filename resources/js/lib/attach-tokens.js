import { get as getCookie } from 'browser-cookies';

const handleForm = (doc, tokenInput, xsrfToken) => {
  const xsrfInput = doc.createElement('input');

  xsrfInput.setAttribute('type', 'hidden');
  xsrfInput.setAttribute('name', '_xsrf_token');
  xsrfInput.setAttribute('value', xsrfToken);

  const parent = tokenInput.parentNode;

  parent.insertBefore(xsrfInput, tokenInput);
  parent.removeChild(tokenInput);
};

const getXsrfToken = async () => {
  const xsrfToken = getCookie('XSRF-TOKEN');

  if (xsrfToken) {
    return xsrfToken;
  }

  await fetch('/cache-evader/ping', {
    method: 'GET',
    credentials: 'same-origin',
  });

  return getCookie('XSRF-TOKEN');
}

const attachTokens = async (win, doc) => {
  const tokenInputs = [...doc.querySelectorAll('input[name="_token"]')];

  if (!tokenInputs.length) {
    return;
  }

  const xsrfToken = await getXsrfToken();

  tokenInputs.forEach(tokenInput => handleForm(doc, tokenInput, xsrfToken));
};

export default attachTokens;
