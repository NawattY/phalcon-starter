<?php

class NotificationController extends ControllerBase
{
    public function indexAction()
    {
//        $user = new Notification();
//
//        $user->firstname = "Test ACC";
//        $user->lastname = "tester";
//        $user->password = "password";
//        $user->email = "testing@example.com";
//        if($user->create() == false) {
//            echo 'Failed to insert into the database' . "\n";
//            foreach($user->getMessages as $message) {
//                echo $message . "\n";
//            }
//        } else {
//            echo 'Happy Days, it worked';
//        }

        $tests = Notification::find();
        foreach ($tests as $test) {
            echo $test->firstname, '<br>';
        }
    }
}
