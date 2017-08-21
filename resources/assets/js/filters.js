Vue.filter('currency', value => {
    let langage = (navigator.language || navigator.browserLanguage).split('-')[0];

    return (value / 100).toLocaleString(langage, {
        style: 'currency',
        currency: 'gbp'
    });
});

Vue.filter('lastFour', value => {
    return Util.formatLastFour(value);
});

Vue.filter('datetime', value => {
    return moment(value).format('D/M/YYYY HH:mm');
});