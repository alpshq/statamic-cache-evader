import attachTokens from './lib/attach-tokens';

(function(win) {
  const doc = win.document;

  attachTokens(win, doc);
})(global);
