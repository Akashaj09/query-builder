<?php
namespace Classes;

class Message extends DB
{
    public function __construct()
    {
        parent::__construct("messages");
    }
    public function messages(){
        return $this->all();
    }
    public function storeMessage($data){
        return $this->create($data);
    }
    public function search(){
        return $result = $this->where("name", "!=", "Akash")
            ->where("email", "!=", "akashajaj09@gmail.com")
            ->orWhere('name', '=', 'Akash')
            ->select("name, email, address")
            ->get();
    }
}