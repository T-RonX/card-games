class PlayerEvent {
    constructor(connection, target_url, message_handler) {
        this.connection = connection;
        this.target_url = target_url;
        this.message_handler = message_handler
    }

    connect() {
        this.connection.connect((e) => {
            this.received(e)
        });
    }

    received(e) {
        this.message_handler.handle(e);
    }

}