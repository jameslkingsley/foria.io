window.Util = {
    formatLastFour(lastFour) {
        let blanks = '&#9913;&#9913;&#9913;&#9913; '.repeat(3);
        return `${blanks}${lastFour}`;
    },

    formatAsCurrency(value) {
        let langage = (navigator.language || navigator.browserLanguage).split('-')[0];

        return (value / 100).toLocaleString(langage, {
            style: 'currency',
            currency: 'gbp'
        });
    }
}

window.opt = window.optional = (object) => {
    return new Proxy(object || {}, {
        get(target, name) {
            return (name in target)
                ? target[name]
                : null;
        }
    });
}
