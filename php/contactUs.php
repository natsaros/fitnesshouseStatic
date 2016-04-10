<?php

$errors = '';

if (isset($_POST['submit'])) {
    $myemail = 'natsaros@gmail.com';

    $name = $_POST['name'];
    $email_address = $_POST['email'];
    $interested = $_POST['interested'];

    if (empty($name)
        || empty($email_address)
        || empty($interested)
    ) {
        $errors .= "\n Error: all fields are required";
    }

    $goal = $_POST['goal'];

    if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $email_address)) {
        $errors .= "\n Error: Invalid email address";
    }

    //TODO-FIXME : server side error handling,now only html5 form handling
    if (empty($errors)) {
        $to = $myemail;
        $email_subject = "Fitness House Contact from: $name";
        $email_body = "\n
        Name: $name \n
        Email: $email_address\n\n";
        if (!empty($goal)) {
            $email_body .= "\tGoals: \n \t$goal\n\n";
        }
        $email_body .= "\tInterested in : \n \t $interested \n";


        $headers = "From: $myemail\n";
        $headers .= "Reply-To: $email_address";
        mail($to, $email_subject, $email_body, $headers);


        header('Location:' . $REQUEST_URI . 'index.php?id=contact');
    }
}
?>

<div class="container-fluid belowHeader text-center">
    <div class="row row-no-padding">
        <div class="col-sm-12">
            <div class="heroHeader grayish">
                <div class="headerTitle">
                    <p>LET'S TALK ABOUT FITNESS</p>
                    <div class="titlesBorder"></div>
                </div>
                <div class="heroSubTitle">Tell us about why you want to join us</div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <form action="index.php?id=contact" method="post">
        <div class="formContainer">
            <div class="row">
                <div class="col-sm-12 text-center">
                    <div class="headerTitle">
                        <p>Επικοινωνήστε μαζί μας</p>
                        <div class="titlesBorder"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4 form-group">
                    <input class="form-control" id="name" name="name" placeholder="Ονοματεπώνυμο *" type="text"
                           required>
                </div>
                <div class="col-sm-4 form-group">
                    <input class="form-control" id="email" name="email" placeholder="Email *" type="email" required>
                </div>
                <div class="col-sm-4 form-group">
                    <input class="form-control" id="phone" name="phone" placeholder="Τηλέφωνο" type="text">
                </div>
            </div>
            <div class="row">
                <div class="col-sm-8 form-group">
                <textarea class="form-control" id="comments" name="goal"
                          placeholder="Τί θέλετε να επιτύχετε; Ποιος είναι ο στόχος;"
                          rows="5"></textarea>
                </div>
                <div class="col-sm-4 form-group">
                <textarea class="form-control" id="interest" name="interested"
                          placeholder="Για ποιές υπηρεσίες μας ενδιαφέρεστε; *" rows="5"></textarea>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-3">
                    <button class="btn btn-block btn-default" type="submit" onclick="postContact(this)">Αποστολή
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="container-fluid text-center">
    <div class="row row-no-padding brown_background">
        <div class="col-sm-6">
            <div class="generalTextContainer">
                <div class="textHolder">
                    <div class="textHolderInside">
                        <div class="headerTitle">
                            <p>Βρείτε το studio μας</p>
                            <div class="titlesBorder"></div>
                        </div>

                        <div class="contactInfo">
                            <p>
                                Ξεκινήστε να γυμνάζεστε σωστά, με άτομα πιστοποιημένα στο είδος τους! Αγαπάτε το σώμα
                                σας
                                και μάθετε γιατί πρέπει να γυμνάζεστε!<br><br>

                                Χαριλάου Τρικούπη 17, 16675 Γλυφάδα, Ελλάδα<br>
                                Τηλ: 6976582735<br>
                                Email: pkasfiki@gmail.com
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div id="googleMap" class="mapContainer"></div>
        </div>
    </div>
</div>