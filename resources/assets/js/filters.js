Vue.filter('currency', value => {
    if (value === null) return null;
    return Util.formatAsCurrency(value);
});

Vue.filter('locale', value => {
    if (value === null) return null;
    return value.toLocaleString();
});

Vue.filter('config', key => {
    let value = Foria.config;

    for (let k of key.split('.')) {
        value = value[k];
    }

    return value;
});

Vue.filter('lastFour', value => {
    return Util.formatLastFour(value);
});

Vue.filter('datetime', value => {
    return moment(value).format('D/M/YYYY HH:mm');
});

Vue.filter('date', value => {
    return moment(value).format('D/M/YYYY');
});

Vue.filter('calendar', value => {
    return moment(value).format('MMMM YYYY');
});

Vue.filter('age', value => {
    return moment().diff(value, 'years');
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
