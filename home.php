<?php include('db_connect.php') ?>
<?php
$twhere ="";
if($_SESSION['login_type'] != 1)
  $twhere = "  ";
?>

  <?php 

    $where = "";
    if($_SESSION['login_type'] == 2){
      $where = " where manager_id = '{$_SESSION['login_id']}' ";
    }elseif($_SESSION['login_type'] == 3){
      $where = " where concat('[',REPLACE(user_ids,',','],['),']') LIKE '%[{$_SESSION['login_id']}]%' ";
    }
     $where2 = "";
    if($_SESSION['login_type'] == 2){
      $where2 = " where p.manager_id = '{$_SESSION['login_id']}' ";
    }elseif($_SESSION['login_type'] == 3){
      $where2 = " where concat('[',REPLACE(p.user_ids,',','],['),']') LIKE '%[{$_SESSION['login_id']}]%' ";
    }
    ?>
        
      <div class="row">
        <div class="col-md-12">
        <div class="card card-outline card-success">
          <div class="card-header">
            <b>Project Progress</b>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table m-0 table-hover">
                <thead>
                  <th>#</th>
                  <th>PROJECT</th>
                  <th>CLIENT</th>
                  <th>PROJECT LEADER</th>
                  <th>START DATE</th>
						      <th>DUE DATE</th>
                  <th>PROGRESS</th>
                  <th>STATUS</th>
                  <th>ACTION</th>
                </thead>
                <tbody>
                <?php
                $i = 1;
                $stat = array("Pending","Started","On-Progress","On-Hold","Over Due","Done");
                $where = "";
                if($_SESSION['login_type'] == 2){
                  $where = " where manager_id = '{$_SESSION['login_id']}' ";
                }elseif($_SESSION['login_type'] == 3){
                  $where = " where concat('[',REPLACE(user_ids,',','],['),']') LIKE '%[{$_SESSION['login_id']}]%' ";
                }
                $qry = $conn->query("SELECT * FROM project_list  $where order by name asc");
                while($row= $qry->fetch_assoc()):
                  $prog= 0;
                $tprog = $conn->query("SELECT * FROM task_list where project_id = {$row['id']}")->num_rows;
                $cprog = $conn->query("SELECT * FROM task_list where project_id = {$row['id']} and status = 3")->num_rows;
                $prog = $tprog > 0 ? ($cprog/$tprog) * 100 : 0;
                $prog = $prog > 0 ?  number_format($prog,2) : $prog;
                $prod = $conn->query("SELECT * FROM user_productivity where project_id = {$row['id']}")->num_rows;
                if($row['status'] == 0 && strtotime(date('Y-m-d')) >= strtotime($row['start_date'])):
                if($prod  > 0  || $cprog > 0)
                  $row['status'] = 2;
                else
                  $row['status'] = 1;
                elseif($row['status'] == 0 && strtotime(date('Y-m-d')) > strtotime($row['end_date'])):
                $row['status'] = 4;
                endif;
                  ?>
                  <tr>
                      <td>
                         <?php echo $i++ ?>
                      </td>
                      <td>
                          <a>
                              <?php echo ucwords($row['name']) ?>
                          </a>
                      </td>
                      <td>
							            <p><?php echo ucwords($row['client']) ?></p>
					          	</td>
                      <td>
							            <p><?php echo ucwords($row['user_ids']) ?></p>
					          	</td>
                      <td><?php echo date("d M Y",strtotime($row['start_date'])) ?></td>
						          <td><?php echo date("d M Y",strtotime($row['end_date'])) ?></td>
                      <td class="project_progress">
                          <div class="progress progress-sm">
                              <div class="progress-bar bg-green" role="progressbar" aria-valuenow="57" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $prog ?>%">
                              </div>
                          </div>
                          <small><b>
                              <?php echo $prog ?>% Complete</b>
                          </small>
                      </td>
                      <td class="project-state">
                          <?php
                            if($stat[$row['status']] =='Pending'){
                              echo "<span class='badge badge-secondary'>{$stat[$row['status']]}</span>";
                            }elseif($stat[$row['status']] =='Started'){
                              echo "<span class='badge badge-primary'>{$stat[$row['status']]}</span>";
                            }elseif($stat[$row['status']] =='On-Progress'){
                              echo "<span class='badge badge-info'>{$stat[$row['status']]}</span>";
                            }elseif($stat[$row['status']] =='On-Hold'){
                              echo "<span class='badge badge-warning'>{$stat[$row['status']]}</span>";
                            }elseif($stat[$row['status']] =='Over Due'){
                              echo "<span class='badge badge-danger'>{$stat[$row['status']]}</span>";
                            }elseif($stat[$row['status']] =='Done'){
                              echo "<span class='badge badge-success'>{$stat[$row['status']]}</span>";
                            }
                          ?>
                      </td>
                      <td>
                        <a class="btn btn-primary btn-sm" href="./index.php?page=view_project&id=<?php echo $row['id'] ?>">
                              <i class="fas fa-folder">
                              </i>
                              View
                        </a>
                      </td>
                  </tr>
                <?php endwhile; ?>
                </tbody>  
              </table>
            </div>
          </div>
        </div>
        </div>
      </div>
