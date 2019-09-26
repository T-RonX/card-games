class DiffCalculator
{
    static cardDiff(a, b) {
        let a_tmp = this.cardUnique(a);
        let b_tmp = this.cardUnique(b);

        return this.cleanCardIntersect(a_tmp.filter(c => !b_tmp.includes(c)));
    }

    static cardUnique(a) {
        let a_tmp = [];
        let b_tmp = [];

        for (const card of a) {
            if (card in a_tmp) {
                a_tmp[card]++;
            } else {
                a_tmp[card] = 1;
            }
        }

        for (const card of a) {
            b_tmp.push(`${a_tmp[card]--}*${card}`);
        }

        return b_tmp;
    }

    static cleanCardIntersect(a) {
        a.forEach((part, c, a) => {
          a[c] = a[c].split('*', 2)[1];
        });

        return a;
    }
}