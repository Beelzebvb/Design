<?php

include 'database_handler.php';

function aresetAndNotEmpty($elements)
{
    foreach ($elements as $element) {
        if (!isset($element) || empty($element))
            return false;
    }

    return true;
}

function userexists($uid)
{
    global $dbh;

    if (filter_var($uid, FILTER_VALIDATE_EMAIL)) {
        $q = 'SELECT id FROM users WHERE email = :email;';
        $stmt = $dbh->prepare($q);

        $status = $stmt->execute(
            array(
                'email' => $uid
            )
        );

        if ($status) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['id']) {
                return true;
            }

            return false;
        } else {
            return false;
        }
    } else {
        $q = 'SELECT id FROM users WHERE pseudo = :pseudo;';
        $stmt = $dbh->prepare($q);

        $status = $stmt->execute(
            array(
                'pseudo' => $uid
            )
        );

        if ($status) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['id']) {
                return true;
            }

            return false;
        } else {
            return false;
        }
    }

    return false;
}

function checkuserpassword($uid, $password)
{
    global $dbh;

    if (filter_var($uid, FILTER_VALIDATE_EMAIL)) {

        $q = 'SELECT password FROM users WHERE email = :email;';
        $stmt = $dbh->prepare($q);
        $status = $stmt->execute(
            array(
                'email' => $uid
            )
        );

        if ($status) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['password']) {
                return password_verify($password, $result['password']);
            }
        } else {
            return false;
        }
    } else {
        $q = 'SELECT password FROM users WHERE pseudo = :pseudo;';
        $stmt = $dbh->prepare($q);
        $status = $stmt->execute(
            array(
                'pseudo' => $uid
            )
        );

        if ($status) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['password']) {
                return password_verify($password, $result['password']);
            }
        } else {
            return false;
        }
    }

    return false;
}

function createUser($firstname, $lastname, $pseudo, $email, $age, $pwd, $sport)
{

    global $dbh;

    $q = 'INSERT INTO users VALUES(:id,:firstname, :lastname, :email, :age, :pwd, :hash, :ip, :mt_points, :sport, :plan, :role, :status,  :pseudo, :profile_picture);';
    $stmt = $dbh->prepare($q);

    $response = $stmt->execute(
        array(
            'id' => getMaxID('users') + 1,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'pseudo' => $pseudo,
            'email' => $email,
            'age' => $age,
            'pwd' => password_hash($pwd, PASSWORD_DEFAULT),
            'hash' => md5(rand(0, 1000)),
            'ip' => ip2long('127.0.0.1'),
            'mt_points' => 100,
            'sport' => $sport,
            'plan' => 1,
            'role' => 1,
            'status' => 1,
            'profile_picture' => NULL

        )
    );

    if ($response) {
        return true;
    }

    return false;
}

function logUser($uid, $password)
{
    session_unset();

    $userData = getUserDataByUID($uid);



    $_SESSION['uid'] = $userData['uid'];
    $_SESSION['username'] = $userData['pseudo'];
    $_SESSION['mt_points'] = $userData['mt_points'];
    $_SESSION['ip'] = long2ip($userData['ip']);
    $_SESSION['role'] = $userData['current_role'];
    $_SESSION['status'] = $userData['current_status'];
    $_SESSION['sport'] = $userData['fav_sport'];

    return true;
}

function getProfilePicture($uid)
{
    global $dbh;

    $q = 'SELECT profile_picture FROM users WHERE id = :id;';
    $stmt = $dbh->prepare($q);
    $status = $stmt->execute(
        array(
            'id' => $uid
        )
    );
    if ($status) {
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['profile_picture']) {

            return $result['profile_picture'];
        }
    }

    return false;
}

function getUserDataByUID($uid)
{
    global $dbh;

    if (!userexists($uid))
        return false;

    if (filter_var($uid, FILTER_VALIDATE_EMAIL)) {

        $q = 'SELECT
        users.id as uid,
        users.first_name,
        users.last_name,
        users.pseudo,
        users.email,
        users.age,
        users.mt_points,
        users.fav_sport,
        users.current_plan,
        users.current_role,
        users.current_status,
        users.ip,
        user_roles.id as rid,
        user_roles.name,
        user_roles.description,
        user_roles.color
        FROM user_roles
        INNER JOIN users
        ON user_roles.id = users.current_role
        WHERE users.email = :email;';

        $stmt = $dbh->prepare($q);

        $status = $stmt->execute(
            array(
                'email' => $uid
            )
        );

        if ($status) {
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($result) == 0) {
                return false;
            }

            return $result[0];
        } else {
            return false;
        }
    } else {
        $q = 'SELECT
        users.id as uid,
        users.first_name,
        users.last_name,
        users.pseudo,
        users.email,
        users.age,
        users.mt_points,
        users.fav_sport,
        users.current_plan,
        users.current_role,
        users.current_status,
        user_roles.id as rid,
        user_roles.name,
        user_roles.description,
        user_roles.color
        FROM user_roles
        INNER JOIN users
        ON user_roles.id = users.current_role
        WHERE users.pseudo = :pseudo;';

        $stmt = $dbh->prepare($q);

        $status = $stmt->execute(
            array(
                'pseudo' => $uid
            )
        );

        if ($status) {
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($result) == 0) {
                return false;
            }

            return $result[0];
        } else {
            return false;
        }
    }
    return false;
}

function getMaxID($table)
{
    global $dbh;

    $q = 'SELECT MAX(id) FROM ' . $table . ';';
    $stmt = $dbh->prepare($q);
    if ($stmt->execute()) {
        $result = $stmt->fetch();

        if ($result[0])
            return $result[0];

        return 0;
    }

    return false;
}

function getAllUsers()
{
    global $dbh;

    $q = 'SELECT id,
    first_name,
    last_name, 
    email,
    age,
    mt_points,
    fav_sport,
    current_plan,
    current_role,
    current_status 
    FROM users;';

    $stmt = $dbh->prepare($q);

    if ($stmt->execute()) {
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) == 0) {
            return false;
        }

        return $result;
    }

    return false;
}

function getCardsByUID($uid)
{
    global $dbh;

    $q = 'SELECT *, basketball_cards.id as bid
                FROM basketball_cards INNER JOIN basketball_inventory ON basketball_cards.id = basketball_inventory.basketball_card WHERE basketball_inventory.user = :uid;';
    $stmt = $dbh->prepare($q);
    $status = $stmt->execute(
        array(
            'uid' => $uid
        )
    );

    if ($status) {
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }
    return false;
}

function addCard(
    $uid,
    $id,
    $fullName,
    $jerseyNumber,
    $headshotURL,
    $position,
    $team,
    $teamABV,
    $league,
    $gp,
    $min,
    $fg,
    $tp,
    $ft,
    $reb,
    $ast,
    $blk,
    $stl,
    $pts,
    $overall
) {
    global $dbh;

    if (!cardexists($id)) {

        $q = 'INSERT INTO basketball_cards
    (id,
     full_name,
     jersey_number,
     headshot_url,
     position,
     team,
     league,
     gp,
     min,
     fg_pct,
     tp_pct,
     ft_pct,
     reb,
     ast,
     blk,
     stl,
     pts,
     overall,
     team_abv
     ) 
     VALUES(
:id,
:full_name,
:jersey_number,
:headshot_url,
:position,
:team,
:league,
:gp,
:min,
:fg,
:tp,
:ft,
:reb,
:ast,
:blk,
:stl,
:pts,
:overall,
:teamABV
);';
        $stmt = $dbh->prepare($q);



        $status = $stmt->execute(
            array(
                'id' => $id,
                'full_name' => $fullName,
                'jersey_number' => $jerseyNumber,
                'headshot_url' => $headshotURL,
                'position' => $position,
                'team' => $team,
                'league' => $league,
                'gp' => $gp,
                'min' => $min,
                'fg' => $fg,
                'tp' => $tp,
                'ft' => $ft,
                'reb' => $reb,
                'ast' => $ast,
                'blk' => $blk,
                'stl' => $stl,
                'pts' => $pts,
                'overall' => $overall,
                'teamABV' => $teamABV

            )
        );

        if ($status) {
            return addCardToInventory($uid, $id);
        }
    } else {
        return addCardToInventory($uid, $id);
    }
    return false;
}

function addCardToInventory($UID, $cardID, $inventory = 'basketball_inventory')
{

    global $dbh;

    $q = 'INSERT INTO ' . $inventory .
        ' VALUES (
        :id,
        :user_id,
        :card_id);';
    $stmt = $dbh->prepare($q);
    $status = $stmt->execute(
        array(
            'id' => getMaxID('basketball_inventory') + 1,
            'user_id' => $UID,
            'card_id' => $cardID
        )
    );
    if ($status) {
        return true;
    }
    return false;
}

function cardexists($id, $sport_cards = 'basketball_cards')
{
    global $dbh;

    $q = 'SELECT 1 FROM ' . $sport_cards . ' WHERE id = :id;';
    $stmt = $dbh->prepare($q);
    $status = $stmt->execute(
        array(
            'id' => $id
        )
    );

    if ($status) {
        return ($stmt->fetch(PDO::FETCH_ASSOC));
    }

    return false;
}

function getUsersAndRoles()
{
    global $dbh;

    $q = 'SELECT
    users.id as uid,
    users.first_name,
    users.last_name,
    users.pseudo,
    users.email,
    users.age,
    users.mt_points,
    users.fav_sport,
    users.current_plan,
    users.current_role,
    users.current_status,
    user_roles.id as rid,
    user_roles.name,
    user_roles.description,
    user_roles.color
    FROM user_roles
    INNER JOIN users
    ON user_roles.id = users.current_role;';

    $stmt = $dbh->prepare($q);

    if ($stmt->execute()) {
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) == 0) {
            return false;
        }

        return $result;
    }

    return false;
}

function getUserByRole($role)
{
    global $dbh;

    $q = 'SELECT id,
    first_name,
    last_name,
    pseudo,
    email,
    age,
    mt_points,
    fav_sport,
    current_plan,
    current_role,
    current_status
    FROM users WHERE current_role = :role;';
    $stmt = $dbh->prepare($q);

    $status = $stmt->execute(
        array(
            'role' => $role
        )
    );

    if ($status) {
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) == 0) {
            return false;
        }

        return $result;
    }

    return false;
}

function searchUser($input)
{
    global $dbh;

    $users = getUsersAndRoles();
    $user = ['abdou'];

    foreach ($users as $key => $data) {
        if (
            strpos(strtolower($data['last_name']), strtolower($input)) !== false
            || strpos(strtolower($data['first_name']), strtolower($input)) !== false
        ) {
            array_push($user,  $data);
        }
    }

    return $user;
}

function getUserAndRoleByUID($uid)
{
    global $dbh;

    $q = 'SELECT
    users.id as uid,
    users.first_name,
    users.last_name,
    users.pseudo,
    users.email,
    users.age,
    users.mt_points,
    users.fav_sport,
    users.current_plan,
    users.current_role,
    users.current_status,
    user_roles.id as rid,
    user_roles.name,
    user_roles.description,
    user_roles.color,
    user_roles.roles_privileges
    FROM user_roles
    INNER JOIN users
    ON user_roles.id = users.current_role
    WHERE users.id = :uid;';

    $stmt = $dbh->prepare($q);
    $status = $stmt->execute(
        array(
            'uid' => $uid
        )
    );

    if ($status) {
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['uid']) {
            return $result;
        }
    }

    return false;
}

function getUserInfo($info)
{
    global $dbh;

    $q = 'SELECT ' . $info . ' FROM users;';
    $stmt = $dbh->prepare($q);

    if ($stmt->execute()) {
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) > 0) {
            return $result;
        }
    }

    return false;
}

function getRoleName($user_id)
{
    global $dbh;

    $q = 'SELECT name FROM user_roles INNER JOIN users ON user_roles.id = users.current_role AND users.id = :user_id;';
    $stmt = $dbh->prepare($q);
    $status = $stmt->execute(
        array(
            'user_id' => $user_id
        )
    );
    if ($status) {
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['name'] ? $result['name'] : false;
    }

    return false;
}

function getRoleByID($uid)
{
    global $dbh;

    $q = 'SELECT * FROM user_roles WHERE id = (SELECT current_role FROM users WHERE users.id = :uid);';
    $stmt = $dbh->prepare($q);
    $status = $stmt->execute(
        array(
            'uid' => $uid
        )
    );

    if ($status) {
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (count($result) > 0) {
            return $result;
        }
    }

    return false;
}

function updateUserRow(
    $table,
    $row,
    $value,
    $id
) {

    global $dbh;

    $q = 'UPDATE ' . $table . ' 
    SET ' . $row . ' = "' . $value . '" 
    WHERE id = :id;';

    $stmt = $dbh->prepare($q);
    $status = $stmt->execute(
        array(
            'id' => $id
        )
    );
    if ($status) {
        return true;
    }
    return false;
}
