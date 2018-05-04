<?php

/**
* Mailer object
* Used to send the email, process all user data, create project objects and
* output the sent email
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
    $this->links = $_POST['project_link'];
    return;
  }

  /**
  * prepareEmail()
  * The prepareEmail() method calls all other functions to create the email
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
    // Create a new project for each alt text
    for ($idx = 0; $idx < sizeof($this->alts); $idx++) {
      $this->projects[$idx] = new Project($idx, $this->makeUserInputSafe($this->alts[$idx]), $this->makeUserInputSafe($this->titles[$idx]), $this->replaceEndOfLine($this->makeUserInputSafe($this->texts[$idx])), $this->makeUserInputSafe($this->links[$idx]));
    }
    // Sets email headers, generates the HTML message and sends the email
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
      <!-- Makes the email appear better on mobile -->
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>HTML EMAIL</title>
    </head>
    <body style="margin: 0;">
      <!-- Table to contain the email so it is displayed as intended -->
      <table border="0" width="100%" cellpadding="0" cellspacing="0" id="main" style="font-family: Open Sans, sans-serif; margin: auto;">
        <!-- used to give a grey bg -->
        <tr class="header" style="font-family: Open Sans, sans-serif; background-color: #FAFAFA;" bgcolor="#FAFAFA">
          <!-- cell spans two columns -->
          <td colspan="2" style="font-family: Open Sans, sans-serif; opacity: .5; font-size: 0.8em; line-height: 1.8em; color: #666666; padding: 0.9em 0 0.9em 15px;">
            Grooup | eCommerce Consultancy
          </td>
        </tr>
        <tr style="font-family: Open Sans, sans-serif;">
          <!-- spans two columns, holds the Grooup logo -->
          <td colspan="2" style="font-family: Open Sans, sans-serif;">
            <a href="http://grooup.com" style="font-family: Open Sans, sans-serif;"><img src="https://projects.michaelbarrows.co.uk/html-email/img/grooup-logo.png" width="200px" style="font-family: Open Sans, sans-serif;"></a>
          </td>
        </tr>
        <!-- table row that holds the greeting -->
        <tr class="content" style="font-family: Open Sans, sans-serif;">
          <td colspan="2" style="font-family: Open Sans, sans-serif; width: 35%; padding-right: 2.5%;" width="35%">
              <p class="indent" style="font-family: Open Sans, sans-serif; padding-left: 5%; padding-right: 5%;">Hello ' . $name . '</p>
              <p class="indent" style="font-family: Open Sans, sans-serif; padding-left: 5%; padding-right: 5%">' . $content . '</p>
          </td>
        </tr>';

        foreach ($this->projects as $project) {
          $message .= '
        <!-- table tow, holds a single project -->
        <tr class="content" style="font-family: Open Sans, sans-serif;">
          <td style="font-family: Open Sans, sans-serif; width: 35%; padding-right: 2.5%;" width="35%">
            <!-- project image -->
            <img src="https://projects.michaelbarrows.co.uk/html-email/' . $project->getProjectImage() . '" alt="' . $project->getAltText() .  '" width="90%" style="font-family: Open Sans, sans-serif; border-radius: 7px; margin: 15px 0 15px 15%;">
          </td>
          <td style="font-family: Open Sans, sans-serif; width: 60%; vertical-align: top;" width="60%" valign="top">
            <!-- project text (title, text and link) -->
            <h1 style="font-family: Open Sans, sans-serif; font-size: 2em; padding-left: 10px; padding-right: 0; color: #000000;">' . $project->getProjectTitle() . '</h1>
            <p style="font-family: Open Sans, sans-serif; padding-left: 10px; padding-right: 0; color: #000000;">' . $project->getProjectText() . '</p>
            <p style="font-family: Open Sans, sans-serif; padding-left: 10px; padding-right: 0; color: #000000;"><a href="' . $project->getProjectLink() . '" style="font-family: Open Sans, sans-serif; color: #50b853; font-weight: bold; font-size: 0.9em; text-decoration: none;">View Project &gt;&gt;</a></p>
          </td>
        </tr>
        ';
      }

      $message .= '
        <!-- Row that holds the email signature -->
        <tr class="content" style="font-family: Open Sans, sans-serif;">
          <td colspan="2" style="font-family: Open Sans, sans-serif; width: 35%; padding-right: 2.5%;" width="35%">
            <p class="indent" style="font-family: Open Sans, sans-serif; padding-left: 5%; padding-right: 5%;">' . $signature . '</p>
          </td>
        </tr>
        <!-- Copyright footer -->
        <tr class="footer content" style="font-family: Open Sans, sans-serif; background-color: #181818;" bgcolor="#181818">
          <td colspan="2" style="font-family: Open Sans, sans-serif; color: #FFFFFF; line-height: 3em; text-align: center; opacity: .5; width: 35%; padding-right: 2.5%;" width="35%" align="center">
            &copy; Michael Barrows 2018
          </td>
        </tr>
      </table>
    </body>
    </html>';

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
  * The makeUserInputSafe() method removes whitespace &other characters from the
  * userInput, removes backslashes, and converts special HTML charachtes such
  * as quotes and ampersands to HTML entities
  */
  function makeUserInputSafe($userInput) {
    $userInput = trim($userInput);
    $userInput = stripslashes($userInput);
    $userInput = htmlspecialchars($userInput);
    return $userInput;
  }

  /**
  * setHeaders()
  * The setHeaders() method is used to set the email's headers, such as
  * Mime type, type of email (HTML) and sets the sender
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
  * __construct($imageNumber, $alt. $title, $text, $link)
  * The __construct() method is used to assign user input to properties and
  * calls the checkFileIsImage and storeProjectImage methods
  */
  public function __construct($imageNumber, $alt, $title, $text, $link) {
    $this->imageName = $_FILES["project_image"]["name"][$imageNumber];
    $this->imageTmpName = $_FILES["project_image"]["tmp_name"][$imageNumber];
    $this->alt = $alt;
    $this->title = $title;
    $this->text = $text;
    $this->link = $link;
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
      print_r($this);
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

  /**
  * getProjectLink()
  * The getProjectLink() method returns the project link from the link property
  */
  public function getProjectLink() {
    return $this->link;
  }
}?>
<!-- HTML output -->
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
        // Checks if the page request came from the form
        if($_SERVER["REQUEST_METHOD"] == "POST") {
          // Creates the new mailer object
          $email = new Mailer();
          // Calls the prepareEmail function to process all user input and send the email
          $email->prepareEmail();
          ?>
          <h2>Your email was sent.</h2>
          <!-- Prints the name, email address and subject -->
          <p class="left">To: <?php echo $email->getName();?> &lt;<?php echo $email->getEmailAddress();?>&gt;</p>
          <p class="left">Subject: <?php echo $email->getSubject(); ?></p>
          <?php
          // Prints the email message that was sent
          echo $email->getMessage();
        } else { ?>
          <!-- Error message if the page was accessed directly -->
          <p>Please submit the form to send the email</p>
          <p><a href="index.html">Go Back</a>
        <?php }
      ?>
    </section>
    <footer>
      <p>&copy; Michael Barrows 2018</p>
    </footer>
  </body>
</html>
