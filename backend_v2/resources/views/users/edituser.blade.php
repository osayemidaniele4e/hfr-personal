<div class="modal fade" id="editUser" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Update User Information</h4>
                <div class='notifications top-right'></div>
            </div>
            <div class="modal-body">

                <div class="panel-body">
                    <form method="POST" action="{{ route('updateuser') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="firstname1" class="col-md-4 col-form-label text-md-right">{{ __('Fist Name') }}
                                <font color="red">*</font>
                            </label>

                            <div class="col-md-8">
                                <input id="firstname1" type="text" class="form-control" name="firstname1"
                                    value="{{ old('firstname') }}" required autofocus>

                                <span class="text-danger">
                                    <strong id="firstname-error1"></strong>
                                </span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="lastname1" class="col-md-4 col-form-label text-md-right">{{ __('Last Name') }}
                                <font color="red">*</font>
                            </label>

                            <div class="col-md-8">
                                <input id="lastname1" type="text" class="form-control" name="lastname1"
                                    value="{{ old('lastname1') }}" required>

                                <span class="text-danger">
                                    <strong id="lastname-error1"></strong>
                                </span>
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="email1"
                                class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }} <font
                                    color="red">*</font></label>

                            <div class="col-md-8">
                                <input id="email1" type="email" class="form-control" name="email1"
                                    value="{{ old('email1') }}" disabled>

                                <span class="text-danger">
                                    <strong id="email-error1"></strong>
                                </span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="mobile1"
                                class="col-md-4 col-form-label text-md-right">{{ __('Mobile Number') }}</label>

                            <div class="col-md-8">
                                <input id="mobile1" type="text"
                                    class="form-control{{ $errors->has('mobile1') ? ' is-invalid' : '' }}"
                                    name="mobile1" value="{{ old('mobile1') }}" data-inputmask='"mask": "0999-999-9999"'
                                    data-mask>

                                <span class="text-danger">
                                    <strong id="mobile-error1"></strong>
                                </span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="job1"
                                class="col-md-4 col-form-label text-md-right">{{ __('Job Title') }}</label>

                            <div class="col-md-8">
                                <input id="job1" type="text" class="form-control" name="job1"
                                    value="{{ old('job1') }}">

                                <span class="text-danger">
                                    <strong id="job-error1"></strong>
                                </span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="organisation1"
                                class="col-md-4 col-form-label text-md-right">{{ __('Organisation') }}</label>

                            <div class="col-md-8">
                                <input id="organisation1" type="text" class="form-control" name="organisation1"
                                    value="{{ old('organisation1') }}">

                                <span class="text-danger">
                                    <strong id="org-error1"></strong>
                                </span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="role1" class="col-md-4 col-form-label text-md-right">{{ __('User Role') }}
                                <font color="red">*</font>
                            </label>
                            <div class="col-md-8">
                                <select class="form-control select2" class="form-control" id="role1" name="role1[]"
                                    data-placeholder="Select Role" required data-width="100%">
                                    @foreach (getRoles() as $role)
                                        <option value="{{ $role->id }}"
                                            {{ $role->id == old('role1') ? 'selected' : '' }}>{{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-danger">
                                    <strong id="role-error1"></strong>
                                </span>
                            </div>
                            <input id="UserID" name="UserID" type="hidden">
                        </div>
                        <div class="form-group row">
                            <label for="state_id1"
                                class="col-md-4 col-form-label text-md-right">{{ __('State Permission') }} <font
                                    color="red">*</font></label>
                            <div class="col-md-8">
                                <select class="form-control select2" class="form-control" id="state_id1"
                                    name="state_id1" data-placeholder="Select State" required data-width="100%">
                                    @if (Auth::user()->state_id == 1)
                                        <option value="1">All States</option>
                                    @endif
                                    @foreach (getAssignedState() as $s)
                                        <option value="{{ $s->id }}"
                                            {{ $s->id == old('state_id1') ? 'selected' : '' }}>{{ $s->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-danger">
                                    <strong id="state-id"></strong>
                                </span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="state_id"
                                class="col-md-4 col-form-label text-md-right">{{ __('LGA Permission') }} <font
                                    color="red">*</font></label>
                            <div class="col-md-8">
                                <select multiple class="form-control select2" class="form-control" id="lga_id1"
                                    name="lga_id1[]" data-placeholder="Select LGA" required data-width="100%">
                                    <option value = "1000">All LGAs</option>
                                    @foreach (getAssignedLga() as $lga)
                                        <option value="{{ $lga->id }}"
                                            {{ in_array($lga->id, old('lga_id1', $selectedLgas ?? [])) ? 'selected' : '' }}>
                                            {{ $lga->name }}
                                        </option>
                                    @endforeach

                                </select>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" id="update" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>

            </div>


        </div><!-- /.modal-content -->
    </div><!--/.modal-dialog -->
</div><!--/.modal -->
