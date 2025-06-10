/* eslint-disable func-names */
const $ = require("jquery");

const assignHeadline = function (element) {
  // endpoint
  const endpoint = element.data("endpoint");
  if (!endpoint) return;
  element.load(endpoint, () => {
    const onCompleteEvent = element.data("complete-event");
    if (onCompleteEvent) {
      window.dispatchEvent(new Event(`onCmsHeadlineLoaded-${  onCompleteEvent}`));
    }
  });
};

// eslint-disable-next-line import/prefer-default-export
export const cmsHeadline = () => {
  $(".js_cms-headline").each(function () {
    assignHeadline($(this));
  });
};
