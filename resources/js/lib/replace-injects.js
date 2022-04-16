const replaceInjects = (win, doc) => {
  const injects = [...doc.querySelectorAll('.cache-evader-inject')];

  if (!injects.length) {
    return;
  }

  injects.forEach(wrapper => handleInject(win, doc, wrapper));
};

const handleInject = async (win, doc, wrapper) => {
  const url = decodeURIComponent(wrapper.dataset.url);

  const params = {};

  (url.split('?')?.[1] || '').split('&').forEach(pair => {
    pair = pair.split('=');
    if (pair.length < 2) {
      return;
    }

    params[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1]);
  });

  const beforeEvent = new CustomEvent('cacheEvaderBeforeInject', {
    bubbles: true,
    cancelable: true,
    detail: {
      url,
      params,
    },
  });

  wrapper.dispatchEvent(beforeEvent);

  if (beforeEvent.defaultPrevented) {
    return;
  }

  const response = await fetch(url, {
    method: 'GET',
    credentials: 'same-origin',
  });

  const html = await response.text();

  if (!html.trim().length) {
    wrapper.parentNode.removeChild(wrapper);
    return;
  }

  let element = toReplaceableNode(doc, html);

  [...wrapper.attributes].forEach(attr => {
    let value = attr.value;

    if (attr.name === 'class' && element.hasAttribute(attr.name)) {
      value += ' ' + element.getAttribute(attr.name);
    }

    element.setAttribute(attr.name, value);
  });

  element.dataset.resolved = '';

  element = readdScripts(doc, element);

  wrapper.parentNode.replaceChild(element, wrapper);

  const afterEvent = new CustomEvent('cacheEvaderAfterInject', {
    bubbles: true,
    cancelable: false,
    detail: {
      url,
      params,
      response,
    },
  });

  element.dispatchEvent(afterEvent);
};

const toReplaceableNode = (doc, html) => {
  const div = doc.createElement('div');

  div.innerHTML = html;

  if (div.children.length === 1) {
    return div.children[0];
  }

  return div;
}

const readdScripts = (doc, el) => {
  [...el.querySelectorAll('script')].forEach(oldScript => {
    const newScript = doc.createElement('script');

    [...oldScript.attributes].forEach(attr => newScript.setAttribute(attr.name, attr.value));

    newScript.appendChild(doc.createTextNode(oldScript.innerHTML));

    oldScript.parentNode.replaceChild(newScript, oldScript);
  });

  return el;
};

export default replaceInjects;
