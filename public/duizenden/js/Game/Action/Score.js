class Score {
    static update(players, rounds) {
        const tbody = $('#scoreboard');
        let new_body = '';
        let score_counter = [];

        for (const player of players) {
            score_counter[player.id] = 0;
        }

        let i = 1;
        for (const round of rounds) {
            let tr = `<tr><td style="vertical-align: top;">#${i}</td>`;

            for (const player of players) {
                let td = '<td>';
                const round_score = (round[player.id].score - round[player.id].hand) + round[player.id].round_finish_extra_points;
                score_counter[player.id] += round_score;
                td += score_counter[player.id];

                if (i > 1) {
                    td = '<br/>';
                    td = `<span style="font-size: 10px;">${(round_score < 0 ? '-' : '+') + round_score}</span>`;
                }

                td += '</td>';
                tr += td;
            }

            tr += '</tr>';
            new_body += tr;
            ++i;
        }

        tbody.empty();
        tbody.append(new_body);
    }
}