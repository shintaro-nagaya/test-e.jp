import 'select2';
import 'select2/dist/css/select2.min.css';

export const jqSelect2 = () => {
    const select = $('.js_select2');
    if(!select.length) return;

    select.select2({
        width: 'auto'
    });
}