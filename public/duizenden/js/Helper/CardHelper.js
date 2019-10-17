class CardHelper{
    static getValueFromId(id) {
        const matches = this.match(id);
        return matches[2] + matches[3];
    }

    static getBackColorFromId(id) {
        const matches = this.match(id);
        return this.createColorFromColorId(matches[1]);
    }

    static createColorFromColorId(id) {
        switch (id.toUpperCase()) {
            case 'R':
                return 'red';

            case 'B':
                return 'blue';

            case 'K':
                return 'black';
        }
    }

    static match(id) {
        return /^([a-z\d]{1})([SHDCXY]{1})([0-9]{1,2}|[JQKA]{1})$/i.exec(id);
    }
}