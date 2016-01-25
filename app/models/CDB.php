<?php
namespace Cdb;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class User extends \User {

    public function managements()
    {
        return $this->hasMany('Cdb\Management', 'user_id', 'id' );
    }

    public function subordinates()
    {
        return $this->hasMany('Cdb\Management', 'boss_id', 'id');
    }

    public function services()
    {
        return $this->hasMany('Cdb\Service', 'user_id', 'id');
    }

    public function member()
    {
        return $this->hasOne('Cdb\Member', 'user_id', 'id')->cdb();
    }

    public function contact()
    {
        return $this->hasOne('Cdb\Contact', 'user_id', 'id');
    }

}

class Service extends Eloquent {

    use SoftDeletingTrait;

    protected $table = 'cdb.dbo.services';

    public $timestamps = true;

    protected $fillable = array('user_id', 'role', 'area', 'country', 'district');

    protected $guarded = array('id');

    public function user() {
        return $this->hasOne('Cdb\User', 'id', 'user_id');
    }

    public function list_district() {
        return $this->hasOne('Cdb\District', 'code', 'district');
    }

    public function neighbors() {
        return $this->hasMany('Cdb\Service', 'area', 'area');
    }

}

class Management extends Eloquent {

    use SoftDeletingTrait;

    protected $table = 'cdb.dbo.management';

    public $timestamps = true;

    protected $fillable = array('user_id', 'boss_id');

    protected $guarded = array('id');

    public function user() {
        return $this->hasOne('Cdb\User', 'id', 'user_id');
    }

    public function boss() {
        return $this->hasOne('Cdb\User', 'id', 'boss_id');
    }

    public function managements() {
        return $this->hasMany('Cdb\Management', 'user_id', 'boss_id');
    }

}

class Contact extends Eloquent {

    protected $table = 'cdb.dbo.contact';

    public $timestamps = true;

    protected $fillable = array('user_id', 'country', 'district', 'address', 'emergency_name', 'emergency_relation', 'emergency_phone');

    protected $guarded = array('id');

    public function user() {
        return $this->hasOne('Cdb\User', 'id', 'user_id');
    }

}

class District extends Eloquent {

    protected $table = 'cdb.dbo.list_districts';

    public $timestamps = true;

    protected $fillable = array('country_name', 'code', 'name');

    protected $guarded = array('id');

}

class Baby extends Eloquent {

    use SoftDeletingTrait;

    protected $table = 'cdb.dbo.babys';

    public $timestamps = true;

    protected $dates = ['deleted_at'];

    protected $softDelete = true;

    protected $fillable = array('identifier', 'name', 'phone', 'gender', 'birthday', 'country', 'village', 'address', 'interviewer_id', 'quit');

    protected $guarded = array('id');

    public function interviewer () {
        return $this->hasOne('Cdb\User', 'id','interviewer_id');
    }

    public function turn_records () {
        return $this->hasMany('Cdb\Turn_record', 'baby_id');
    }

    public function visit_parent () {
        return $this->hasMany('Cdb\Visit_parent', 'baby_id', 'id');
    }

}

class Turn_record extends Eloquent {

    protected $table = 'cdb.dbo.turn_records';

    public $timestamps = true;

    protected $fillable = array('baby_id', 'sender_title', 'sender', 'recipient_title', 'recipient', 'reason', 'finish', 'notification');

    protected $guarded = array('id');

}

class Menber_auth extends Eloquent {

    use SoftDeletingTrait;

    protected $table = 'cdb.dbo.member_auth';

    public $timestamps = true;

    protected $fillable = array('user_id', 'app_id', 'write', 'reads');

    protected $guarded = array('id');

}

class Wave extends Eloquent {

    protected $table = 'cdb.dbo.waves';

    public $timestamps = true;

    protected $fillable = array('month', 'start', 'ending', 'wait_start', 'method');

    protected $guarded = array('id');

}

class Visit_parent extends Eloquent {

    protected $table = 'cdb.dbo.visit_parents';

    public $timestamps = true;

    protected $fillable = array('baby_id', 'wave', 'interviewer_id', 'file_id', 'style', 'finish', 'created_at', 'updated_at');

    protected $guarded = array('id');

    public function baby() {
        return $this->hasOne('Cdb\Baby', 'id');
    }

    public function waves() {
        return $this->hasMany('Cdb\Wave', 'id', 'condition');
    }

    public function sorting() {
        return $this->hasMany('Cdb\Ques_repository', 'visit_id', 'id');
    }

}

class Ques_repository extends Eloquent {

    protected $table = 'cdb.dbo.ques_repository';

    public $timestamps = true;

    protected $fillable = array('page_id', 'ques_id', 'visit_id', 'value', 'string', 'created_at', 'baby_id', 'created_by');

    protected $guarded = array('id');

}

class Ques_rule extends Eloquent {

    protected $table = 'cdb.dbo.ques_rules';

    public $timestamps = true;

    protected $fillable = array('day', 'night', 'week', 'ques2', 'doc_id', 'doc_name');

    protected $guarded = array('id');

}

class Struct_cdb {

    static function management($user)
    {
        return array(
            'id'                => (int)$user->id,
            'actived'           => (bool)$user->active && (bool)$user->menber->active,
            'disabled'          => (bool)$user->disabled,
            'password'          => $user->password=='',
            'email'             => $user->email,
            'name'              => $user->username,
            'services'          => $user->services->map(function($service) {
                                        isset($service->district) ? $district_name = $service->list_district->name : $district_name = '';
                                        return ['area' => $service->area, 'role' => $service->role, 'country' => $service->country, 'district' => $district_name];
                                    })->all(),
            'managements'       => $user->managements->map(function($management) {
                                                                    return ['name' => $management->boss->username, 'id' => $management->boss->id];
                                                                })->all(),
            'phone'             => is_null($user->menber) ? '' : $user->menber->phone,
            'tel'               => is_null($user->menber) ? '' : $user->menber->tel,
            'created_at'        => $user->created_at->toDateString()
        );
    }

    static function divert_status($record,$user)
    {
        if($record->sender_title ==1 && $record->recipient_title == 3){
            if($record->sender == $user->id){
                return  $status = array('id'=>'2', 'code'=>'轉出中');
            }
            elseif($record->recipient == $user->id && $record->notification == 0){
                return  $status = array('id'=>'2', 'code'=>'訪員轉入通知');
            }
            elseif($record->recipient == $user->id && $record->notification == 1){
                return  $status = array('id'=>'6', 'code'=>'需處理名單');
            }
            else{
                return $status = array('id'=>'0', 'code'=>'名單處理中');
            }
        }

        // elseif($record->sender_title ==2 && $record->recipient_title == 1){
        //  if($record->sender == $user->id){
        //      return  $status = array('id'=>'7', 'code'=>'配置中');
        //  }
        //  elseif($record->recipient == $user->id && $record->notification == 0){
        //      return  $status = array('id'=>'3', 'code'=>'轉入通知');
        //  }
        //  else{
        //      return $status = array('id'=>'0', 'code'=>'名單處理中');
        //  }
        // }

        // elseif($record->sender_title ==2 && $record->recipient_title == 3){
        //  if($record->sender == $user->id){
        //      return  $status = array('id'=>'8', 'code'=>'轉出中');
        //  }
        //  elseif($record->recipient_title == 3 && $record->notification == 0){
        //      return  $status = array('id'=>'9', 'code'=>'轉入通知');
        //  }
        //  elseif($record->recipient_title == 3 && $record->notification == 1){
        //      return  $status = array('id'=>'11', 'code'=>'需處理名單');
        //  }
        //  else{
        //      return $status = array('id'=>'00000', 'code'=>'名單處理中');
        //  }
        // }

        elseif($record->sender_title ==3 && $record->recipient_title == 1){
            if($record->sender == $user->user_id){
                return  $status = array('id'=>'12', 'code'=>'配置中');
            }
            elseif($record->recipient == $user->id && $record->notification == 0){
                return  $status = array('id'=>'5', 'code'=>'助理轉入通知');
            }
            elseif($record->recipient == $user->id && $record->notification == 1){
                return  $status = array('id'=>'6', 'code'=>'需處理名單');
            }
            else{
                return $status = array('id'=>'0', 'code'=>'名單處理中');
            }
        }

        elseif($record->sender_title ==3 && $record->recipient_title == 3){
            if($record->sender == $user->id){
                return  $status = array('id'=>'13', 'code'=>'轉出中');
            }
            elseif($record->recipient == $user->id && $record->notification == 0){
                return  $status = array('id'=>'10', 'code'=>'助理轉入通知');
            }
            elseif($record->recipient == $user->id && $record->notification == 1){
                return  $status = array('id'=>'11', 'code'=>'需處理名單');
            }
            else{
                return $status = array('id'=>'0', 'code'=>'名單處理中');
            }
        }
    }

    static function count($wave,$baby)
    {

        $count = 0;

        $date =0;
        $createds = [];
        $check = [];

        $am =0;
        $pm =0;
        $night =0;
        $time =0;

        $record = $baby->visit_parent()->where('type', '=', 2)->orderBy('created_at', 'DESC')->first();

        if(!empty($record)){
            $count = $baby->visit_parent()->where('type', '=', 2)->where('wave', '=', $record->wave)->count();
            if($record->wave == $wave->id -1){
                return $baby->quit;
            }

            elseif($record->wave == $wave->id){
                if($record->success == 1){
                    Baby:: updateOrCreate(array('id'=>$baby->id), array('quit'=>0));
                    return 0;
                }
                elseif($record->success != 1 && $count < 5){
                    return $baby->quit;
                }
                elseif($record->success != 1 && $count >= 5){
                    $createds = $baby->visit_parent()->where('wave', '=', $record->wave)->select('created_at')->get();
                    foreach ($createds as $created) {
                        $part = $created->created_at;
                        if($part->hour <= 12){ $am++;}
                        elseif($part->hour > 12 && $part->hour <= 18){ $pm++;}
                        elseif($part->hour > 18){ $night++;}
                        ($am ==1 || $pm ==1 || $night ==1) && $time++;
                        !empty($check) ? !in_array($created->created_at->toDateString(), $check) && array_push($check, $created->created_at->toDateString()) && $date++
                                       : array_push($check, $created->created_at->toDateString()) && $date++;
                    }
                    // $a = Baby::find(5)->select('quit')->get();
                    // var_dump($a->toArray());exit;
                    //var_dump($date);exit;

                    if($date >=3 && $time>=2){
                        Baby:: updateOrCreate(array('id'=>$baby->id), array('quit'=>1));
                        return 1;
                    }
                    else { return $baby->quit;}
                }
            }
        } else {
            return 0;
        }

    }

}