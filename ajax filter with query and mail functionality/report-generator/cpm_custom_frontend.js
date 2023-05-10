var startDate;
var endDate;
var course;
// jQuery('.cpm_exporter').hide();
jQuery(document).ready(function () {
  jQuery('input[name="daterange"]').daterangepicker(
    {
      opens: "left",
    },
    function (start, end, label) {
      startDate = start.format("YYYY-MM-DD");
      endDate = end.format("YYYY-MM-DD");
      // console.log(startDate);
      // console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
    }
  );
});

function changeCourse() {
  course = jQuery("#cpm-course").find(":selected").val();
  if (course) {
    jQuery.ajax({
      type: "POST",
      url: pluginajax.ajaxpluginurl,
      data: {
        course: course,
      },
      beforeSend: function () {
        // Show image container
        jQuery("#loader").show();
      },
      success: function (data) {
        getComboA(); //If course->quiz doesnot exit table, will dissapear
        jQuery("#cpm-quiz").html(data);
      },
      error: function (data) {
        console.log(data);
      },
      complete: function (data) {
        // Hide image container
        jQuery("#loader").hide();
      },
    });
  } else {
    jQuery("#datafetch").hide();
    jQuery("#cpm-quiz").html(
      '<option value="" class="fdasf">Select Course first</option>'
    );
  }
}

function getComboA() {
  var location = jQuery("#cpm-location").find(":selected").val();
  var days = jQuery("#cpm-days").find(":selected").val();
  var quiz = jQuery("#cpm-quiz").find(":selected").val();

  if (location && startDate && endDate && course && quiz) {
    jQuery.ajax({
      url: exporterajax.ajaxurl,
      type: "post",
      data: {
        action: "data_fetch",
        location: location,
        days: days,
        quiz: quiz,
        startDate: startDate,
        endDate: endDate,
        course: course,
      },
      beforeSend: function () {
        // Show image container
        jQuery("#loader").show();
        jQuery(".content_only_for_mail").hide();
      },
      success: function (data) {
        jQuery("#datafetch").show();
        jQuery("#datafetch").html(data);
        jQuery(".content_only_for_mail").hide();
        jQuery('.cpm_exporter').show();
      },
      complete: function (data) {
        // Hide image container
        jQuery("#loader").hide();
        jQuery(".content_only_for_mail").hide();
      },
    });
  } else {
    jQuery("#datafetch").hide();
  }
}

jQuery(document).on("submit", "#email_manager_form", function (e) {
  e.preventDefault();
  jQuery(".manager_details").show();
  // var table = jQuery("#user_table").html();
  jQuery(".content_only_for_mail").show();
  var table = jQuery("#main_report").html();
  jQuery(".content_only_for_mail").hide();
  var email = jQuery("#email_text").val();
  var html_data = encodeURIComponent(table);
  jQuery.ajax({
    url: exporterajax.ajaxurl,
    type: "post",
    // dataType: 'string',
    data: {
      action: "mail_function",
      table: html_data,
      mail: email,
    },
    beforeSend: function () {
      // Show image container
      jQuery("#loader").show();
      jQuery(".content_only_for_mail").hide();
    },
    success: function (data) {
      jQuery(".manager_details").hide();
      jQuery(".content_only_for_mail").hide();
      jQuery("#mail_sent").show();
    },
    error: function (e) {
      e.preventDefault();
    },
    complete: function (data) {
      // Hide image container
      jQuery("#loader").hide();
    },
  });
});



tags = [];

jQuery(document).on("keydown", "#fakeinput", function (e) {
  var tagname = jQuery("#tags-input").val().replace(",", "");

  if (tagname.length < 1 && e.keyCode == 8) {
    if (tags.length > 0) {
      deleteLastTagFromArray();
    }
  }
});

jQuery(document).on("keyup", "#fakeinput", function (e) {
  var tagname = jQuery("#tags-input").val().replace(",", "");
  if (e.keyCode == 188 || e.keyCode == 13) {
    console.log("enter or comma used");
    if (tagname.length > 2 && !doesTagExist(tagname)) {
      makeTag(tagname);
      addTagToArray(tagname);
    }
  }
});

function doesTagExist(tagname) {
  var o = 0;
  var len = tags.length;
  for (; o < len; o++) {
    if (tagname == tags[o]) {
      return ture;
    }
  }
  return false;
}

function makeTag(tagname) {
  var tagTemplate =
    '<div id="' + tagname + '" class="tag">' + tagname + "</div>";
  jQuery(tagTemplate).insertBefore("#tags-input");
  jQuery("#tags-input").val("");
}

function removeTag(tagname) {
  var tagid = "#" + tagname;
  jQuery(tagid).remove();
}

function addTagToArray(tagname) {
  // code to come
  tags.push(tagname);
//   console.log(tags);
//   demoUpdateArrayOnScreen();
}

function deleteLastTagFromArray() {
  var last = tags.length - 1;
  var lastTag = tags[last];
  // alert(tags[last]);
  removeTag(lastTag);
  deleteTagFromArray(lastTag);

//   demoUpdateArrayOnScreen(); //demo
}

function deleteTagFromArray(tagname) {
  var i = 0;
  var len = tags.length;
  for (; i < len; i++) {
    if (tagname == tags[i]) {
      tags.splice(i, 1);
    }
  }
//   demoUpdateArrayOnScreen(); // demo only
}

jQuery(".fake-input--box").on("click", ".tag", function (e) {
  console.log("delete: " + this.id);
  removeTag(this.id);
  deleteTagFromArray(this.id);
});

// demo only
// function demoUpdateArrayOnScreen() {
// //   console.log('here'+tags);
//   var tree = JSON.stringify(tags, null, 2);
//   jQuery("#showarray").html("<pre>" + tree + "</pre>");
// }

/***
 *
 * for exporter
 */

jQuery(document).on("click", ".cpm-table-export", function (event) {
  event.preventDefault();
  var location = jQuery("#cpm-location").find(":selected").val();
  var days = jQuery("#cpm-days").find(":selected").val();
  var quiz = jQuery("#cpm-quiz").find(":selected").val();

  jQuery.ajax({
    type: "POST",
    url: exporterajax.ajaxurl,
    data: {
      action: "cpm_comment_exporter_csv_files",
      location: location,
      days: days,
      quiz: quiz,
      startDate: startDate,
      endDate: endDate,
      course: course,
    },
    success: function (response) {
      if (jQuery.trim(response) == "") {
        alert("There is no Comments For this Feed");
      } else {
        /*
         * Make CSV downloadable
         */
        var downloadLink = document.createElement("a");
        var fileData = ["\ufeff" + response];

        var blobObject = new Blob(fileData, {
          type: "text/csv;charset=utf-8;",
        });

        var url = URL.createObjectURL(blobObject);
        var csv_file_name = "student_report_exporter";
        downloadLink.href = url;
        downloadLink.download = csv_file_name +".csv";
        // console.log(downloadLink.download);

        /*
         * Actually download CSV
         */
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
      }
    },
  });
});


/**
 * 
 * for custom multiple mail
 */

// jQuery(document).on("submit", "#custom_email_form", function (e) {
//   e.preventDefault();
//   jQuery(".custom_greeting_msg").show();
//   jQuery(".admin_greeting_msg").hide();
//   jQuery(".manager_details").show();
//   // var table = jQuery("#user_table").html();
//   jQuery(".content_only_for_mail").show();
//   var table = jQuery("#main_report").html();
//   jQuery(".content_only_for_mail").hide();
//   var html_data = encodeURIComponent(table);
//   jQuery.ajax({
//     url: exporterajax.ajaxurl,
//     type: "POST",
//     // dataType: 'string',
//     data: {
//       action: "custom_mail_function",
//       table: html_data,
//       mail: tags,
//     },
//     beforeSend: function () {
//       // Show image container
//       jQuery("#loader").show();
//       jQuery(".content_only_for_mail").hide();
//     },
//     success: function (data) {
//       jQuery(".manager_details").show();
//       jQuery(".content_only_for_mail").hide();
//       jQuery("#mail_sent").show();
//       jQuery(".custom_greeting_msg").hide();
//       jQuery(".admin_greeting_msg").show();
//       jQuery(".fake-input--box .tag").remove();
//     },
//     error: function (e) {
//       e.preventDefault();
//     },
//     complete: function (data) {
//       // Hide image container
//       jQuery("#loader").hide();
//     },
//   });
// });
