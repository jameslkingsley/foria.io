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
