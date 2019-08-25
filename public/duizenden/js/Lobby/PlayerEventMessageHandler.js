class PlayerEventMessageHandler {
    constructor(uuid_placeholder, accept_url, decline_url, new_game_url, join_game_url) {
        this.uuid_placeholder = uuid_placeholder;
        this.accept_url = accept_url;
        this.decline_url = decline_url;
        this.new_game_url = new_game_url;
        this.join_game_url = join_game_url;
    }

    handle(e) {
        const data = JSON.parse(e.data);

        switch (data.type) {
            case 'new_invitation':
                this.invitationReceived(data.data);
                break;
            case 'invitation_accepted_by_all':
                this.invitationAcceptedByAll(data.data);
                break;
            case 'game_started':
                this.gameStarted(data.data);
                break;
        }
    }

    invitationReceived(data) {
        const accepted = confirm(`${data.inviter} invited you to a new game. Do you accept this invitation?`);

        if (accepted) {
            $.get(this.accept_url.replace('000', encodeURIComponent(data.id)), null, () => {
                //alert('accepted');
            });
        } else {
            $.get(this.decline_url.replace('000', encodeURIComponent(data.id)), null, () => {
                alert('declined');
            });
        }
    }

    invitationAcceptedByAll(data) {
        const create_game = confirm('All players accepted your invite. Do you want to setup a new game now?');

        if (create_game) {
            window.location = this.new_game_url.replace('000', encodeURIComponent(data.id));
        } else {
            alert('You can start the game at any time via the invitations page.');
        }
    }

    gameStarted(data)
    {
        const join_game = confirm(`${data.inviter} created a new game, do you want to join now?`);

        if (join_game) {
            window.location = this.join_game_url.replace(this.uuid_placeholder, encodeURIComponent(data.id));
        } else {
            alert('You can join the game at any time via the invitations page.');
        }
    }
}
