window.Util = {
    formatLastFour(lastFour) {
        let blanks = '&#9913;&#9913;&#9913;&#9913; '.repeat(3);
        return `${blanks}${lastFour}`;
    }
}
