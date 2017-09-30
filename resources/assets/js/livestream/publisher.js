import EventMap from './events';

class Publisher {
    constructor(config) {
        this.config = config;
        this.redConfig = _.extend({}, LiveStream.config, this.config.driver);

        this.driver = new Red5.RTCPublisher();
        this.driver.on('*', e => this.pipeEvent(e));

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
        this.driver.init(this.redConfig)
            .then(() => {
                // On broadcast started, subscribe.
                // this.driver.on(Red5.PublisherEventTypes.PUBLISH_START, subscribe);
                return this.driver.publish();
            })
            .then(() => {
                console.log('Publishing!');
            })
            .catch(error => {
                console.error('Could not publish: ' + error);
            });
    }

    stop() {
        return ajax.delete('/api/broadcast');
    }

    onStart() {
        return ajax.post('/api/broadcast')
            .then(r => {
                if (! 'onBroadcasting' in this.config) return;
                this.config.onBroadcasting(r.data);
            });
    }
}

export default Publisher
