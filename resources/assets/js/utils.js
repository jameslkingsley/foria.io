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

window.nonNullQuery = (url, params) => {
    if (!_.endsWith(url, '/')) {
        url += '/';
    }

    let parts = [];

    for (let key in params) {
        let value = params[key];
        if (value === null) continue;
        parts.push(`${key}=${value}&`);
    }

    if (parts.length) {
        url += '?';
        for (let part of parts) {
            url += part;
        }
    }

    if (_.endsWith(url, '&')) {
        url = _.trimEnd(url, '&');
    }

    return url;
}
