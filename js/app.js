  $(document).ready(function(){
    // When the plus link is clicked, a new project field is added
    $('a.plus').click(function(){
      // prevents the clicked link from doing anything
      event.preventDefault();
      // Inserts a h2 before the submit
      $("<h2>Project Settings</h2>").insertBefore("#submit");
      // Inserts a project image upload field before the submit
      $("<label><span>Project Image:</span><input type='file' name='project_image[]'><span><br>Please note: images should be at least 200px wide</span></label>").insertBefore("#submit");
      // Inserts an alternative project image text field before the submit
      $("<label><span>Alternative Image Text:</span><input type='text' name='project_image_alt[]'' placeholder='Alternative Image Text'></label>").insertBefore("#submit");
      // Inserts a project title field before the submit
      $("<label><span>Project Title:</span><input type='text' name='project_title[]' placeholder='Project Title'></label>").insertBefore("#submit");
      // Inserts a project link field before the submit
      $("<label><span>Project Link:</span><input type='text' name='project_link[]' placeholder='Project Link'></label>").insertBefore("#submit");
      // Inserts a project text field before the submit
      $("<label><span>Project Text:</span><textarea name='project_text[]' placeholder='Project Text'></textarea></label>").insertBefore("#submit");
      // Inserts an add another project link before the submit
      $("<p><a href='#' class='plus'>Add another project &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+</a></p>").insertBefore("#submit");
      // inserts a horizontal rule
      $("<hr>").insertBefore("#submit");
    });
});
