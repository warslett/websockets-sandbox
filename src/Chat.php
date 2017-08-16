<?php


namespace BiffBangPow\WebSockets;


use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class Chat implements MessageComponentInterface
{

    /**
     * @var \SplObjectStorage
     */
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    /**
     * When a new connection is opened it will be passed to this method
     * @param  ConnectionInterface $conn The socket/connection that just connected to your application
     * @throws \Exception
     */
    function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        $message = "{$conn->resourceId} has joined the chat room";
        echo $message . PHP_EOL;

        $this->sendToAll("SYSTEM", $message);
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
     * @param  ConnectionInterface $conn The socket/connection that is closing/closed
     * @throws \Exception
     */
    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        $message = "{$conn->resourceId} has disconnected";
        echo $message . PHP_EOL;
        $this->sendToAll("SYSTEM", $message);
    }

    /**
     * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
     * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
     * @param  ConnectionInterface $conn
     * @param  \Exception $e
     * @throws \Exception
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }

    /**
     * Triggered when a client sends data through the socket
     * @param  \Ratchet\ConnectionInterface $from The socket/connection that sent the message to your application
     * @param  string $msg The message received
     * @throws \Exception
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        echo sprintf(
            "Connection %d sending message \"%s\" to %d other connections\n",
            $from->resourceId,
            $msg,
            count($this->clients)
        );

        $this->sendToAll($from->resourceId, $msg);
    }

    private function sendToAll($sender, $message)
    {
        foreach ($this->clients as $client) {
            $client->send($sender . ": " . $message);
        }
    }
}
