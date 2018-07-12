class SortTable {
    /**
     * Сортирует таблицу по элементу th.
     *
     * @param th
     *
     * @todo: добавить сохранение сортировки через LocalStorage, придумать способ без ид...
     */
    static sort(th) {

        if (th.nodeName !== 'TH') {
            console.error('Неправильный элемент для сортировки.', th);
        } else {
            let table = this.getTable(th); // элемент таблицы
            let rows = table.getElementsByTagName('tbody')[0]; // список полей

            let reverse = th.dataset.reverce === 'true' || false;
            th.dataset.reverce = (!reverse).toString();
            let index = Array.from(th.parentNode.children).indexOf(th); // текущий индек элемента
            let sort_type = th.dataset.type || 'default'; // тип сортировки
            sort_type = 'sort' + sort_type[0].toUpperCase() + sort_type.slice(1); // функция сортировки.

            // запускаем сортировку и получаем отсортированные элементы.
            let sorted_rows = Array.from(rows.children).sort(this.sortCompare(index, Sort[sort_type], reverse));
            // формируем новый список строк
            let new_rows = document.createElement('tbody');
            for (let i in sorted_rows) {
                new_rows.appendChild(sorted_rows[i]);
            }
            // и заменяем старые строи на новые.
            table.replaceChild(new_rows, rows);
        }
    }

    /**
     * Получает родителя таблицу по дочернему элементую
     *
     * @param el дочерний элемент таблицы
     *
     * @returns {HTMLElement}
     */
    static getTable(el) {
        let parent = el.parentElement;
        while (parent && parent.nodeName !== 'TABLE') {
            parent = parent.parentElement;
        }

        return parent;
    }

    static sortCompare(index, func, reverse) {
        return (a, b) => {
            let ca = a.children[index].innerHTML;
            let cb = b.children[index].innerHTML;

            return func(ca, cb, reverse);
        };
    }
}

/**
 * Класс-helper сортировки.
 */
class Sort {
    static sortInteger(a, b, reverse) {
        let ai = parseInt(a);
        let bi = parseInt(b);

        return Sort.sortDefault(ai, bi, reverse);
    }

    static sortDefault(a, b, reverse)
    {
        let weight = a < b ? -1 : (a > b ? 1 : 0);

        return reverse ? -weight : weight;
    }

    static sortDate(a, b, reverse) {
        let pattern = /(\d{2})\.(\d{2})\.(\d{4})/;
        let ad = Date.parse(a.replace(pattern, '$2/$1/$3'));
        let bd = Date.parse(b.replace(pattern, '$2/$1/$3'));

        return Sort.sortInteger(ad, bd, reverse);
    }

    static sortText(a, b, reverse) {
        let weight = a.localeCompare(b);

        return reverse ? -weight : weight;
    }

    static sortDecimal(a, b, reverse) {
        let ai = parseFloat(a);
        let bi = parseFloat(b);

        return Sort.sortDefault(ai, bi, reverse);
    }
}

export {Sort, SortTable};