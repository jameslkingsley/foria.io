import EventMap from './events';

class Publisher {
    constructor(config) {
        this.config = config;
        this.driverConfig = _.extend({}, LiveStream.config, this.config.driver);

        this.publisher = new Red5.RTCPublisher();
        this.publisher.on('*', e => this.pipeEvent(e));

        this.start();
    }

    pipeEvent(e) {
        let method = EventMap[e.type];

        if (method in this) {
            this[method](e.data);
        }

        if (method in this.config) {
            this.config[method](e.data);
        }
    }

    start() {
        console.log('Starting...');
        this.publisher.init(this.driverConfig)
            .then(publisher => publisher.publish())
            .catch(error => {
                console.error('Could not publish: ' + error);
            });
    }

    stop() {
        console.log('Stopping...');
        ajax.delete('/api/broadcast');
        this.publisher.unpublish();
    }

    onStart() {
        if (! this.config.createNew) return;

        return ajax.post('/api/broadcast')
            .then(r => {
                if (! 'onBroadcasting' in this.config) return;
                this.config.onBroadcasting(r.data);
            });
    }
}

export default Publisher
