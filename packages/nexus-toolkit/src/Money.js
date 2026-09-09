export default class Money {

    static format(value, currencySymbol = "$") {

        const amount = Number(value || 0);

        return `${currencySymbol}${amount.toFixed(2)}`;

    }

    static toNumber(value) {

        const amount = Number(value);

        return Number.isFinite(amount) ? amount : 0;

    }

}
