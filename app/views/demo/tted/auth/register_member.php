<?php

$user = new Project\Teacher\User;
$user->username = $input['name'];
$user->email    = $input['email'];
$user->valid();

$contact = new Plat\Contact(array(
    'title'      => $input['title'],
    'tel'        => $input['tel'],
    'fax'        => $input['fax'],
    'department' => $input['department'],
));

$contact->valid();

$work = new Project\Teacher\Work(['ushid' => $input['sch_id'], 'type' => $input['type_class']]);

try {
    DB::beginTransaction();

    $user->save();

    $member = Plat\Member::firstOrNew(['user_id' => $user->id, 'project_id' => 2]);
    $member->actived = false;

    $user->members()->save($member);
    $user->works()->save($work);
    $member->contact()->save($contact);

    DB::commit();
} catch (\PDOException $e) {
    DB::rollback();
    throw $e;
}

return $member;