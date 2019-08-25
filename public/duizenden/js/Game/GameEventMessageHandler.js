class GameEventMessageHandler {
    constructor(player_id) {
        this.player_id = player_id;
    }

    handle(e) {
        const message = JSON.parse(e.data);

        switch (message.type) {
            case 'refresh':
                this.doRefresh(message.data);
                break;
        }
    }

    doRefresh(data) {
        if (data.cause_player !== this.player_id) {
            location.reload();
        }
    }
}
