const $ = require("jquery");
import {load} from "recaptcha-v3";

export const recaptcha = () => {
  const div = $('.g-recaptcha');
  if(!div.length) return;

  const siteKey = div.data('site-key');
  load(siteKey).then((recaptcha) => {
    recaptcha.execute('submit').then((token) => {
      const hidden = $('<input/>');
      hidden.attr('type', 'hidden');
      hidden.attr('name', 'g-recaptcha-response');
      hidden.val(token);
      div.append(hidden);
    });
  });
}
