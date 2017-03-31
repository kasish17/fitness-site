<?php
session_start();

if(isset($_SESSION['authenticated']))
{
require 'commonFiles/getConnection.php';
//to do the editing part.
if(isset($_POST['confirmEdit'])){
  $exerciseName = mysqli_real_escape_string($db, $_POST['exerciseName']);
  $BodyPart = mysqli_real_escape_string($db, $_POST['BodyPart']);
  $about = mysqli_real_escape_string($db, $_POST['about']);
  $videoLink = mysqli_real_escape_string($db, $_POST['videoLink']);
  $instrument = mysqli_real_escape_string($db, $_POST['instrument']);
  $more = mysqli_real_escape_string($db, $_POST['more']);
  $noSpaceBodyPart = preg_replace('/\s+/', '', $BodyPart);
  $noSpaceExerciseName = preg_replace('/\s+/', '', $exerciseName);
  $id = mysqli_real_escape_string($db, $noSpaceBodyPart."_".$noSpaceExerciseName);

  $checkExercise = "SELECT * FROM project_exercises_list where id = '$id'";
  $result2 = $db->query($checkExercise);
  if ($result2->num_rows == 1) {
    $bodypartupdate = "UPDATE project_exercises_list set instrument = '$instrument', videoAddress='$videoLink', about='$about', moreinfo = '$more' WHERE id = '$id'";
    $bodypartresult = $db->query($bodypartupdate);
    if($bodypartresult === TRUE){
      echo "Update Successful <br>";
    }
    else {
      echo "some error occured<br>" . $db->error;
    }
  }
}

//fetch list of body parts
echo "<script>";
echo "var partsarray = [];";
echo "</script>";

$fetchBodyParts = "SELECT id, name, number_of_exercise FROM project_body_parts";
$result = $db->query($fetchBodyParts);
if ($result->num_rows > 0) {
  echo '<form action="editexercise.php" method="post">';
  echo '<label for="BodyPart" style="width:200px;display: inline-block;">Body Part</label>';
  echo '<select name="BodyPart" id="BodyPart" onchange="bodypartChanged(this)">';
  while($row = $result->fetch_assoc()) {  //bosyy part name.
    echo "<script>";
    echo 'partsarray.push("' . $row["name"] . '");';
    echo "var " . $row["name"] . " = [];";
    $checkExercise = "SELECT * FROM project_exercises_list where bodypart = '" . $row["name"] . "'";
    $result2 = $db->query($checkExercise);
    if ($result2->num_rows == 0) {
      echo $row["name"] . ".push('');";
      echo "hidedivision();";
    }
    else {
      while($row2 = $result2->fetch_assoc()) {  //exercise table.
        echo $row["name"] . ".push(\"" . $row2["name"] . "\");";
        echo "var ". $row2["id"] . "= [];";
        echo $row2["id"] . '.push("' . $row2["instrument"] .'");';
        echo $row2["id"] . '.push("' . $row2["videoAddress"] .'");';
        echo $row2["id"] . '.push("' . $row2["about"] .'");';
        echo $row2["id"] . '.push("' . $row2["moreInfo"] .'");';
      }
    }
    echo "</script>";
    echo '<br><option value="' . $row["name"] . '">' . $row["name"] . "</option>";
  }
  echo '</select>';
  echo '<br><label for="exerciseName" style="width:200px;display: inline-block;">Exercise Name</label>';
  echo '<select name="exerciseName" id="exerciseName" onchange="exerciseChanged(this.value)"></select><br>';
  echo '<div id="division">';
  echo '<label for="about" style="width:200px;display: inline-block;">Little discription</label>';
  echo '<textarea name="about" id="about" rows="4" cols="25"></textarea>';
  echo '<br><label for="videoLink" style="width:200px;display: inline-block;">Video link</label>';
  echo '<input type="text" name="videoLink" id="videoLink" placeholder="link"/><br>';
  echo '<label for="instrument" style="width:200px;display: inline-block;">Instrument Used</label>';
  echo '<input type="text" name="instrument" id="instrument" placeholder="Instrument used" required/><br>';
  echo '<label for="more" style="width:200px;display: inline-block;">Something more</label>';
  echo '<textarea name="more" id="more" rows="3" cols="25" placeholder="in format \'attributename:data.\'(followed by fullstop)"></textarea>';

  echo '<input type="submit" name="confirmEdit" value="Edit Exercise" />';
  echo '</div>';
  echo '</form>';

}
else {
  echo "Add some body parts first.";
}
//js

echo "
<script>
function hidedivision(){
  division.style.visibility = 'hidden';;
}
function showdivision(){
  division.style.visibility = 'visible';
}
function bodypartChanged(selfobj){
  var text = '';
  var tempbodypart = eval(selfobj.value);
for(i=0; i<tempbodypart.length;i++){
  text += \"<option value='\" + tempbodypart[i] + \"'>\" + tempbodypart[i] + \"</option>\";
}
  exerciseChanged(tempbodypart[0], selfobj.value);
  exerciseName.innerHTML = text;
}
function exerciseChanged(exercisename, bodypart){
  if(exercisename != '')
    showdivision();
  else{
    hidedivision();
    return;
  }
  var tempnode = eval(bodypart.replace(/\s/g,'') + '_' + exercisename.replace(/\s/g,''));
  var instr = tempnode[0];
  var vidlnk = tempnode[1];
  var abt = tempnode[2];
  var mr = tempnode[3];

  about.value = abt;
  videoLink.value = vidlnk;
  instrument.value = instr;
  more.value = mr;

}
if(exerciseName.value == '')
  hidedivision();

</script>";
}





?>
