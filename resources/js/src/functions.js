export default {
    parseToDecimal(value = 2, decimals = 2) {
        if (isNaN(parseFloat(value).toFixed(2)) || isNaN(parseInt(decimals)))
            return 0;
        return parseFloat(parseFloat(value).toFixed(parseInt(decimals)));
    },
    //format number to money text
    formatCurrency(value = 0, min = 2, max = 2) {
        value = isNaN(parseFloat(value).toFixed(2)) ? 0 : value;
        min = isNaN(parseInt(min)) ? 0 : min;
        max = isNaN(parseInt(max)) ? 0 : max;
        return Intl.NumberFormat(undefined, {
            minimumFractionDigits: min,
            maximumFractionDigits: max
        }).format(value);
    }
};
