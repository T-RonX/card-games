class Connection {
    constructor(url) {
        this.url = url;
        this.registerOnClose();
    }

    registerOnClose() {
        window.onbeforeunload = () => {
            if (this.event_source.OPEN === this.event_source.readyState) {
                this.event_source.close();
            }
        };
    }

    connect(onMessage) {
        this.event_source = new EventSource(this.url);
        this.event_source.onopen = (e) => { this.onOpen(e); };
        this.event_source.onerror = () => { this.onError(); };
        this.event_source.onmessage = onMessage;
    }

    onOpen() {
        console.log(`Connected to "${this.url}".`);
    }

    onError() {
        console.log(`Connection to "${this.url}" failed.`);
    }
}