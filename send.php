<?php

/**
* Mailer object
*/
class Mailer {
  public $fromName = "MICHAEL BARROWS";
  public $fromEmail = "22686134@go.edgehill.ac.uk";

  /**
  * getUserInput()
  * Gets all submitted user input from the form using the $_POST variable,
  * and assigns as properties of the Mailer object
  */
  function getUserInput() {
    $this->to = $_POST['to'];
    $this->name = $_POST['name'];
    $this->subject = $_POST['subject'];
    $this->content = $_POST['content'];
    $this->signature = $_POST['signature'];
    $this->alts = $_POST['project_image_alt'];
    $this->titles = $_POST['project_title'];
    $this->texts = $_POST['project_text'];
    return;
  }

  /**
  * prepareEmail()
  * The prepareEmail() method
  */
  function prepareEmail() {
    $this->getUserInput();
    // make the user input safe
    $this->to = $this->makeUserInputSafe($this->to);
    $this->name = $this->makeUserInputSafe($this->name);
    $this->subject = $this->makeUserInputSafe($this->subject);
    $this->content = $this->makeUserInputSafe($this->content);
    $this->signature = $this->makeUserInputSafe($this->signature);

    // replace end of line with breaks
    $this->content = $this->replaceEndOfLine($this->content);
    $this->signature = $this->replaceEndOfLine($this->signature);
    for ($idx = 0; $idx < sizeof($this->alts); $idx++) {
      $this->projects[$idx] = new Project($idx, $this->makeUserInputSafe($this->alts[$idx]), $this->makeUserInputSafe($this->titles[$idx]), $this->replaceEndOfLine($this->makeUserInputSafe($this->texts[$idx])));
    }
    $this->headers = $this->setHeaders();
    $this->emailContent = $this->createMessage();
    $this->sendEmail($this->to, $this->subject, $this->emailContent, $this->headers);
  }

  /**
  * getName()
  * The getName() method returns the name of the person to which the email is
  * addressed.
  */
  public function getName() {
    return $this->name;
  }

  /**
  * getEmailAddress()
  * The getEmailAddress() method returns the email address to which the email
  * is being sent
  */
  public function getEmailAddress() {
    return $this->to;
  }

  /**
  * getSubject()
  * The getSubject method returns the subject line for the email.
  */
  public function getSubject() {
    return $this->subject;
  }

  /**
  * getMessage()
  * The getMessage() method returns the entire HTML email sent to the recipient.
  */
  public function getMessage() {
    return $this->message;
  }

  /**
  * createMessage()
  * The createMessage() method assembles the HTML email to be sent to the
  * recipient.
  * The user submitted content is placed into the email in a location relevant
  * for that information.
  */
  function createMessage() {
    $name = $this->name;
    $content = $this->content;
    $signature = $this->signature;
    $message = '
    <!DOCTYPE html>
    <html>
    <head>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>HTML EMAIL</title>
      <!-- <link rel="stylesheet" href="https://michaelbarrows.com/html-email/styles.css"> -->
      <style>
    @font-face {
      font-family: "Open Sans";
      font-weight: 400;
      font-style: normal;
      src: local("Open Sans Regular"), local("OpenSans-Regular"), url(https://fonts.gstatic.com/s/opensans/v15/mem8YaGs126MiZpBA-UFVZ0b.woff2) format("woff2");
      unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
      }
      p {
        text-align: left;
      }
    </style>
    </head>
    <body style="padding: 0; margin: 0;">
      <table border="0" width="80%" cellpadding="0" cellspacing="0" style="font-family: Open Sans, sans-serif; margin: auto;">
        <tr style="font-family: Open Sans, sans-serif; background-color: #FAFAFA;" bgcolor="#FAFAFA">
          <td colspan="2" style="font-family: Open Sans, sans-serif; opacity: .5; font-size: 0.8em; line-height: 1.8em; color: #666666; padding: 0.9em 0 0.9em 15px;">
            Grooup | eCommerce Consultancy
          </td>
        </tr>
        <tr style="font-family: Open Sans, sans-serif;">
          <td width="50%" style="font-family: Open Sans, sans-serif; padding: 15px 0 0 15px;">
            <a href="http://grooup.com" style="font-family: Open Sans, sans-serif;"><img src="https://html-email.michaelbarrows.com/grooup-logo.png" width="200px" style="font-family: Open Sans, sans-serif;"></a>
          </td>
          <td class="nav" style="font-family: Open Sans, sans-serif; vertical-align: top; text-align: right; padding-top: 15px;" valign="top" align="right">
            <a href="#" style="font-family: Open Sans, sans-serif; margin-right: 1.2em; font-weight: 600; text-decoration: none; letter-spacing: .5px; color: #252525; opacity: 0.5; font-size: 0.75em;">GROOUP</a>
            <a href="#" style="font-family: Open Sans, sans-serif; margin-right: 1.2em; font-weight: 600; text-decoration: none; letter-spacing: .5px; color: #252525; opacity: 0.5; font-size: 0.75em;">WORK</a>
            <a href="#" style="font-family: Open Sans, sans-serif; margin-right: 1.2em; font-weight: 600; text-decoration: none; letter-spacing: .5px; color: #252525; opacity: 0.5; font-size: 0.75em;">JOBS</a>
            <a href="#" style="font-family: Open Sans, sans-serif; margin-right: 1.2em; font-weight: 600; text-decoration: none; letter-spacing: .5px; color: #252525; opacity: 0.5; font-size: 0.75em;">CONTACT</a>
          </td>
        </tr>
        <tr style="font-family: Open Sans, sans-serif; padding: 0 15px;">
          <td colspan="2" style="font-family: Open Sans, sans-serif; padding-left: 40px; padding-right: 40px;">
            <p style="font-family: Open Sans, sans-serif; margin-bottom: 30px;">Hello ' . $name . ',</p>
            <p style="font-family: Open Sans, sans-serif;">' . $content . '</p>
          </td>
        </tr>';
        foreach ($this->projects as $project) {
          $message .= '
          <tr style="font-family: Open Sans, sans-serif;">
            <td width="50%" style="font-family: Open Sans, sans-serif; padding-left: 40px;">
              <img src="https://html-email.michaelbarrows.com/' . $project->getProjectImage() . '" alt="' . $project->getAltText() .  '"width="90%" style="font-family: Open Sans, sans-serif; border-radius: 8px; margin-top: 20px;">
            </td>
            <td width="50%" style="font-family: Open Sans, sans-serif; vertical-align: top; padding-left: 30px; padding-top: 20px; padding-right: 40px;" valign="top">
              <h1 style="font-family: Open Sans, sans-serif;">' . $project->getProjectTitle() . '</h1>
              <p style="font-family: Open Sans, sans-serif;">' . $project->getProjectText() . '</p>
            </td>
          </tr>
          ';
        }
        $message .= '<tr style="font-family: Open Sans, sans-serif; padding: 0 15px;">
          <td colspan="2" style="font-family: Open Sans, sans-serif; padding-left: 40px; padding-right: 40px;">
            <p style="font-family: Open Sans, sans-serif;">' . $signature . '</p>
          </td>
        </tr>
        <tr style="font-family: Open Sans, sans-serif; background-color: #181818; padding: 0 15px;" bgcolor="#181818">
          <td colspan="2" style="font-family: Open Sans, sans-serif; color: #FFFFFF; line-height: 3em; text-align: center; opacity: .5;" align="center">
            &copy; Michael Barrows 2018
          </td>
        </tr>
      </table>
    </body>
    </html>
    ';
    $this->message = $message;
    return $message;
  }

  /**
  * replaceEndOfLine($text)
  * The replaceEndOfLine() method uses the str_replace function to identify
  * line breaks in the user submitted data, and replaces these line breaks with
  * the HTML <br> tag so they are not ignored in the email.
  */
  function replaceEndOfLine($text) {
    return str_replace(PHP_EOL, "<br>", $text);
  }

  /**
  * makeUserInputSafe($userInput)
  * The makeUserInputSafe() method does what?
  * ?????????????????????????????????????????
  */
  function makeUserInputSafe($userInput) {
    $userInput = trim($userInput);
    $userInput = stripslashes($userInput);
    $userInput = htmlspecialchars($userInput);
    return $userInput;
  }

  /**
  * setHeaders()
  * The setHeaders() method is used to ?
  * ????????????????????????????????????
  */
  function setHeaders() {
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: " . $this->fromName . "<$this->fromEmail>" . "\r\n";
    return $headers;
  }

  /**
  * sendEmail($to, $subject, $message, $headers)
  * The sendEmail() method is used to send the prepared email to the recipient
  */
  function sendEmail($to, $subject, $message, $headers) {
    mail($to,$subject,$message,$headers);
  }
}

/**
* Project object
*/
class Project {

  /**
  * __construct($imageNumber, $alt. $title, $text)
  * The __construct() method is used to ...
  */
  public function __construct($imageNumber, $alt, $title, $text) {
    $this->imageName = $_FILES["project_image"]["name"][$imageNumber];
    $this->imageTmpName = $_FILES["project_image"]["tmp_name"][$imageNumber];
    $this->alt = $alt;
    $this->title = $title;
    $this->text = $text;
    $this->checkFileIsImage();
    $this->storeProjectImage();
  }

  /**
  * storeProjectImage()
  * The storeProjectImage() method is used to store the user uploaded image
  * within the file system so that once the email is sent, the images can be
  * obtained from the server. In addition to this, this method als checks that
  * all required directories exist (if not, it creates them) and handles
  * duplicate images.
  */
  public function storeProjectImage() {
    $this->imagePath = "images/" . date(Y) . "/" . date(m) . "/" . date(d);
    if (!$this->checkFileExists($this->imagePath)) {
      $this->makeDirectory($this->imagePath);
    }
    $this->replaceSpacesInFilename();
    $idx = 0;
    while ($this->checkFileExists("$this->imagePath/$idx" . "_" . $this->imageName)) {
      $idx++;
    }
    $this->imageName = $idx . "_" . $this->imageName;
    if(!move_uploaded_file($this->imageTmpName, "$this->imagePath/$this->imageName")) {
      die("Image could not be uploaded.");
    }
  }

  /**
  * checkFileExists($file)
  * The checkFileExists() method is used to determine if a file or directory
  * already exists and returns a boolean value.
  */
  function checkFileExists($file) {
    if (file_exists($file)) {
      return true;
    } else {
      return false;
    }
  }

  /**
  * makeDirectory($path)
  * The makeDirectory method is used to create a directory if it does not
  * already exist, and assigns that directory all permissions necessary so that
  * images can be displayed to the sender and recepient.
  */
  function makeDirectory($path) {
    mkdir("$path", 0777, true);
    return;
  }

  /**
  * replaceSpacesInFilename()
  * The replaceSpacesInFilename() method replaces the space character in the
  * image filename with an underscore, so that the space is not replaced with a
  * '+' or '%20' character.
  */
  function replaceSpacesInFilename() {
    $this->imageName =  str_replace(" ", "_", $this->imageName);
    return;
  }

  /**
  * checkFileIsImage()
  * the checkFileIsImage() method checks if the file uploaded by the user is an
  * image, to ensure only images are uploaded. The method prevents further
  * code execution is the file is found not to be an image.
  */
  function checkFileIsImage () {
    if (!getimagesize($this->imageTmpName)) {
      die("Please go back and select an image");
    }
  }

  /**
  * getProjectImage()
  * The getProjectImage() method returns the complete path of the image,
  * and it's filename so it can be accessed.
  */
  public function getProjectImage() {
    return "$this->imagePath/$this->imageName";
  }

  /**
  * getAltText()
  * The getAltText() method returns the images alternative text from the alt
  * property
  */
  public function getAltText() {
    return $this->alt;
  }

  /**
  * getProjectTitle()
  * The getProjectTitle() method returns the title of the project from the title
  * property.
  */
  public function getProjectTitle() {
    return $this->title;
  }

  /**
  * getProjectText()
  * The getProjectText() method returns the text of the project from the text
  * property
  */
  public function getProjectText() {
    return $this->text;
  }
}?>

<!DOCTYPE html>
<html>
  <head>
    <title>HTML Email</title>
    <link rel="stylesheet" href="css/styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  </head>
  <body>
    <header>
      <h1>HTML Email</h1>
    </header>
    <section>
      <?php
        if($_SERVER["REQUEST_METHOD"] == "POST") {
          $email = new Mailer();
          $email->prepareEmail();
          ?>
          <h2>Your email was sent.</h2>
          <p class="left">To: <?php echo $email->getName();?> &lt;<?php echo $email->getEmailAddress();?>&gt;</p>
          <p class="left">Subject: <?php echo $email->getSubject(); ?></p>
          <?php
          echo $email->getMessage();
        } else { ?>
          <p>Please submit the form to send the email</p>
        <?php }
      ?>
    </section>
    <footer>
      <p>&copy; Michael Barrows 2018</p>
    </footer>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="js/app.js"></script>
  </body>
</html>
