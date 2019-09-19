class GameEventMessageHandler {
    constructor(game_id, player_id) {
        this.game_id = game_id;
        this.player_id = player_id;
    }

    handle(e) {
        const message = JSON.parse(e.data);

        if (message.game_id !== this.game_id)
        {
            return;
        }

        if (message.status !== 'ok')
        {
            alert(message.status);
        }

        this.doRefresh(message.data);
    }

    doRefresh(data) {
        //if (data.cause_player !== this.player_id) {
            location.reload();
        //}
    }
}
