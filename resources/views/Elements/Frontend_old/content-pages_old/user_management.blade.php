   <div role="tabpanel" class="tab-pane fade in active" id="Section8">

        <h3 class="text-center"><u>User Management</u></h3>
        <div class="table-responsive">
            <table class="table table-borderless">
                <tr>
                    <td><span class="h3">Edit User Profile</span></td>
                    <td>
                        <form class="form-horizontal" action="/action_page.php">
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="name">Name:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="name" placeholder="Enter name" name="name">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="adr">Address:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="adr" placeholder="Enter address" name="adr">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="email">Email:</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" id="email" placeholder="Enter email" name="email">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="mbl">Mobile:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="mbl" placeholder="Enter Mobile" name="mbl">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button type="submit" class="btn btn-info">Submit</button>
                                </div>
                            </div>
                        </form>
                    </td>
                </tr>
                <tr>
                    <td><span class="h3">Change Password</span></td>
                    <td>Change Password <input class="pwd" type="text" /> <br/><br/>Confirm Password <input class="cpwd" type="text" /></td>
                </tr>
            </table>
        </div>
    </div>