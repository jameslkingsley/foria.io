Vue.filter('currency', value => {
    return Util.formatAsCurrency(value);
});

Vue.filter('lastFour', value => {
    return Util.formatLastFour(value);
});

Vue.filter('datetime', value => {
    return moment(value).format('D/M/YYYY HH:mm');
});

Vue.filter('fromnow', value => {
    return moment(value).fromNow();
});

Vue.filter('todate', value => {
    return moment().to(value);
});

Vue.filter('capitalize', value => {
    return _.capitalize(value);
});

Vue.filter('duration', value => {
    return moment
        .duration(value, 'seconds')
        .format('hh:mm:ss', { trim: false });
});
