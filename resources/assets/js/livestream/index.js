// import Stream from './stream';
import Broadcaster from './broadcaster';
import Viewer from './viewer';

class LiveStream {
    constructor() {
        //
    }

    broadcaster() {
        return new Broadcaster;
    }

    viewer() {
        return new Viewer;
    }
}

export default LiveStream
