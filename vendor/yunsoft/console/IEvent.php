<?php


namespace yun\console;


interface IEvent
{

    /**
     * 客户端连接后回调
     * 返回值为true则不做任何处理,否则断开连接
     * @param $client_id
     * @return mixed 如果返回值不是false或者空值,则会作为断开连接的参数,在断开前发送信息
     * @author hyunsu
     * @time 2019-06-06 10:32
     */
    public function afterConnect($client_id);

    /**
     * 当businessWorker进程启动时触发。每个进程生命周期内都只会触发一次。
     * 可以在这里为每一个businessWorker进程做一些全局初始化工作，例如设置定时器，初始化redis等连接等。
     * 注意：$businessworker->onWorkerStart和Event::onWorkerStart不会互相覆盖，如果两个回调都设置则都会运行。
     * 不要在onWorkerStart内执行长时间阻塞或者耗时的操作，这样会导致BusinessWorker无法及时与Gateway建立连接，造成应用异常(SendBufferToWorker fail. The connections between Gateway and BusinessWorker are not ready错误)。
     * @param $businessWorker businessWorker进程实例
     * @return void 无返回值，任何返回值都会被视为无效的
     * @author hyunsu
     * @time 2019-06-06 09:58
     */
    public function onWorkerStart($businessWorker);

    /**
     * 当客户端连接上gateway进程时(TCP三次握手完毕时)触发的回调函数。
     * @param $client_id client_id固定为20个字符的字符串，用来全局标记一个socket连接，每个客户端连接都会被分配一个全局唯一的client_id。
     *                  如果client_id对应的客户端连接断开了，那么这个client_id也就失效了。当这个客户端再次连接到Gateway时，将会获得一个新的client_id。也就是说client_id和客户端的socket连接生命周期是一致的。
     *                  client_id一旦被使用过，将不会被再次使用，也就是说client_id是不会重复的，即使分布式部署也不会重复。
     *                  只要有client_id，并且对应的客户端在线，就可以调用Gateway::sendToClient($client_id, $data)等方法向这个客户端发送数据。
     * @return void
     * @author hyunsu
     * @time 2019-06-06 09:59
     */
    public function onConnect($client_id);

    /**
     * 在onMessage方法前执行,返回false则不会执行onMessage方法
     * 方法中可以修改接收的参数
     * @param $client_id 全局唯一的客户端socket连接标识
     * @param $recv_data 完整的客户端请求数据,对该数据的修改直接影响onMessage的数据
     * @return mixed
     * @author hyunsu
     * @time 2019-06-06 13:57
     */
    public function beforeMessage($client_id, &$recv_data);

    /**
     * 当客户端发来数据(Gateway进程收到数据)后触发的回调函数
     * @param $client_id 全局唯一的客户端socket连接标识
     * @param $recv_data 完整的客户端请求数据，数据类型取决于Gateway所使用协议的decode方法返的回值类型
     * @return void
     * @author hyunsu
     * @time 2019-06-06 10:02
     */
    public function onMessage($client_id, $recv_data);

    /**
     * 客户端与Gateway进程的连接断开时触发。不管是客户端主动断开还是服务端主动断开，都会触发这个回调。一般在这里做一些数据清理工作。
     * 注意：onClose回调里无法使用Gateway::getSession()来获得当前用户的session数据，但是仍然可以使用$_SESSION变量获得。
     * 注意：onClose回调里无法使用Gateway::getUidByClientId()接口来获得uid，解决办法是在Gateway::bindUid()时记录一个$_SESSION['uid']，onClose的时候用$_SESSION['uid']来获得uid。
     * 注意：断网断电等极端情况可能无法及时触发onClose回调，因为这种情况客户端来不及给服务端发送断开连接的包(fin包)，服务端就无法得知连接已经断开。
     * @param $client_id 全局唯一的client_id
     * @return void
     * @author hyunsu
     * @time 2019-06-06 10:11
     */
    public function onClose($client_id);

    /**
     * 当客户端连接上gateway完成websocket握手时触发的回调函数。
     * 注意：此回调只有gateway为websocket协议并且gateway没有设置onWebSocketConnect时才有效。
     * @param $client_id client_id固定为20个字符的字符串，用来全局标记一个socket连接，每个客户端连接都会被分配一个全局唯一的client_id。
     * @param $data websocket握手时的http头数据，包含get、server等变量
     * @return void
     * @author hyunsu
     * @time 2019-06-06 10:00
     */
    public function onWebSocketConnect($client_id, $data);

}