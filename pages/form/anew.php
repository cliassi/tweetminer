<?php
$content = "<ul class='nav nav-tabs' role='tablist'>
    <li role='presentation' class='active'><a href='#home' aria-controls='home' role='tab' data-toggle='tab'>Simple Job</a></li>
    <li role='presentation'><a href='#profile' aria-controls='profile' role='tab' data-toggle='tab'>Complex Job</a></li>
  </ul>

  <!-- Tab panes -->
  <div class='tab-content'>
    <div role='tabpanel' class='tab-pane active' id='home'>
      <div class='well bs-component'>
        <form class='form-horizontal' method='post'>
          <fieldset>
            <legend>New Job</legend>
            <div class='col-lg-12'>
              <div class='form-group'>
                <label for='name' class='col-lg-2 control-label'>Name</label>
                <div class='col-lg-10'>
                  <input type='text' name='name' id='name' placeholder='e.g. Find happy tweets' class='form-control' value='$job->j_name'>
                </div>
              </div>
            </div>
            <div class='col-lg-6'>
              <div class='form-group'>
              <label for='select' class='col-lg-4 control-label'>Serach Criteria</label>
              <div class='col-lg-8'>
                ".selectEnum("class='form-control' id='select' name='criteria'", 'jobs', 'j_criteria', $job->j_criteria)."
              </div>
            </div>
            <div class='form-group'>
              <label for='retweet' class='col-lg-4 control-label'>Extract Re-tweet?</label>
              <div class='col-lg-8'>
                <input type='checkbox' class='form-control' name='retweet' id='retweet' ".($job->j_retweet?'checked':'')." value='1'/>
              </div>
            </div>
            <div class='form-group'>
              <label for='start_date' class='col-lg-4 control-label'>Start Date</label>
              <div class='col-lg-8'>
                ".dateTimeSelector('start_date', $job->j_startdate)."
              </div>
            </div>
            <div class='form-group'>
              <label for='end_date' class='col-lg-4 control-label'>End Date</label>
              <div class='col-lg-8'>
                ".dateTimeSelector('end_date', $job->j_enddate)."
              </div>
            </div> 
          </div>
          <div class='col-lg-6'>
            <div class='form-group'>
              <label for='keywords' class='col-lg-4 control-label'>Keywords</label>
              <div class='col-lg-8'>
                <textarea class='form-control' name='keywords' id='keywords' placeholer='Keywords' rows='5'>$job->j_keywords</textarea>
                One keyword per line (For hasgtags and mentions, do not include # and @)
              </div>
            </div>  
          </div>
          <div class='col-lg-12'>      
            <div class='form-group'>
              <div class='col-lg-10 col-lg-offset-2'>
                <button type='reset' class='btn btn-default'>Cancel</button>
                <button type='submit' name='save' class='btn btn-primary'>Submit</button>
              </div>
            </div>
          </div>
          </fieldset>
        </form>
      </div>
    </div>
    <div role='tabpanel' class='tab-pane' id='profile'>      
      <div class='well bs-component'>
        <form class='form-horizontal' method='post'>
          <fieldset>
            <legend>New Job</legend>
            <table class='table-responsive' width='100%'>
              <tr><td>Job Name</td><td>: </td><td><input type='text' name='name' id='name' placeholder='e.g. Find happy tweets' class='form-control' value='$job->j_name'></td><td><input type='checkbox' class='' name='retweet' id='retweet' ".($job->j_retweet?'checked':'')." value='1'/> Extract Re-Tweet?</td></tr>
              <tr><td>Country</td><td>: </td><td>
                <input type='text' name='country' id='country' placeholder='e.g. Country Name' class='form-control' value='$job->j_country'></td>
                <td>Logic: <input type='radio' name='lcountry' value='1' /> AND <input type='radio' name='lcountry' value='0' checked /> OR</td></tr>
              ".criteria("City", "city", "", $job->j_city, $job->j_cilogic).
              criteria("Keywords", "keywords", "", $job->j_keywords, $job->j_klogic).
              criteria("Users", "users", "", $job->j_users, $job->j_ulogic).
              criteria("Hashtags", "hashtag", "", $job->j_hashtag, $job->j_hlogic)."
              <tr><td>Mentions</td><td>: </td><td>
                <input type='text' name='mention' id='mention' placeholder='' class='form-control' value='$job->j_mention'></td></tr>
              <tr><td>Start Date</td><td>:</td><td colspan='2'> ".dateTimeSelector('start_date', $job->j_startdate)."</td></tr>
              <tr><td>End Date</td><td>:</td><td colspan='2'> ".dateTimeSelector('end_date', $job->j_enddate)."</td></tr>
              <tr><td colspan='5'><button type='reset' class='btn btn-default'>Cancel</button>
                <button type='submit' name='save' class='btn btn-primary'>Submit</button></td></tr>
            </table>
          </fieldset>
        </form>
      </div>
    </div>
  </div>";

print $content;

function criteria($label, $name, $placeholder, $value, $logic){
  return "<tr><td>$label</td><td>: </td><td>
                <input type='text' name='$name' id='$name' placeholder='$placeholder' class='form-control' value='$value'></td>
                <td>Logic: <input type='radio' value='1' name='l$name' /> AND <input type='radio' value='0' name='l$name' checked /> OR</td></tr>";
}