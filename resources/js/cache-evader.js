import attachTokens from './lib/attach-tokens';
import replaceInjects from './lib/replace-injects';

(function(win) {
  const doc = win.document;

  attachTokens(win, doc);
  replaceInjects(win, doc);
})(global);
