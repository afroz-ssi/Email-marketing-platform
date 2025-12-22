import {ref} from 'vue';
import { router,usePage } from '@inertiajs/vue3'
import moment from 'moment';  // https://momentjs.com/docs/#/parsing/string-format/
moment.calendar = {
    lastDay : '[Yesterday at] LT',
    sameDay : '[Today at] LT',
    nextDay : '[Tomorrow at] LT',
    lastWeek : '[last] dddd [at] LT',
    nextWeek : 'dddd [at] LT',
    sameElse : 'L'
};

const shortBy = ref(false);
class ListHelper {

    sortBy(column){
        shortBy.value = !shortBy.value;
        let shortByy = shortBy.value ? 'asc':'desc';
        router.reload({
        method: 'get',
        data: {fieldName:column ,shortBy: shortByy},
        replace: true,
        });
    }

    setPerPage(perPage) {
        router.reload({
            method: 'get',
            data: { perPage: perPage },
            replace: false,
        });
    }

    setPageNum(page) {
        router.reload({
        method: 'get',
        data: {page:page},
        replace: true,
        });
    }

    getRandomVal = () => {
        let colors = ["success", "info", "warning", "dark", "primary"];
         let cla = 'kt-badge--'+colors[Math.floor(Math.random() * colors.length)];
          let obj = {
            [cla]:true
         }
         return obj;
    }

    dateFormat(date, format){
        return moment(date).format(format)
    }

    dateFormat1(date, format){
        return moment.utc(date).format(format)
    }

    calendarFormat(date){
        return moment(date).calendar()
    }

    getPathUrlParam(){
        const urlPath = window.location.pathname;
        return urlPath.substring(urlPath.lastIndexOf('/') + 1);
    }

}

export default ListHelper = new ListHelper()
