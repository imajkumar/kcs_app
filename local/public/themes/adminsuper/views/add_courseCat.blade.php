<!--begin::Container-->
<div class="container">

    <!--begin::Card-->
    <div class="card card-custom">
        <div class="card-header">
            <div class="card-title">
                <span class="card-icon">
                    <i class="flaticon2-supermarket text-primary"></i>
                </span>
                <h3 class="card-label">Couser Sub Category</h3>
            </div>

        </div>
        <div class="card-body">
            <div class="card-body p-0">
                <div class="row justify-content-center py-8 px-8 py-lg-15 px-lg-10">
                    <div class="col-xl-12 col-xxl-10">
                        <!--begin::Wizard Form-->

                        @if (\Session::has('success'))
                        <div class="alert alert-success">
                            <ul>
                                <li>{!! \Session::get('success') !!}</li>
                            </ul>
                        </div>
                        @endif
                        <form class="form fv-plugins-bootstrap fv-plugins-framework" method="post" action="{{route('uploadFileSubcat')}}" enctype="multipart/form-data">

                        @csrf
                            <div class="row justify-content-center">
                                <div class="col-xl-9">
                                    <!--begin::Wizard Step 1-->
                                    <div class="my-5 step" data-wizard-type="step-content" data-wizard-state="current">
                                       
                                       
                                       <!--begin::Group-->
                                       <div class="form-group row fv-plugins-icon-container">
                                            <label class="col-xl-3 col-lg-3 col-form-label">Course Name</label>
                                            <div class="col-lg-9 col-xl-9">
                                                
                                                <select class="form-control form-control-solid form-control-lg" id="course_id" name="course_id">
                                                <?php 
                                                
                                                $coursArr=DB::table('course_list')->where('is_deleted',0)->get();
                                                foreach ($coursArr as $key => $rowData) {
                                                   ?>
                                                   <option value="{{$rowData->id}}">{{$rowData->name}}</option>
                                                   <?php
                                                }

                                                ?>
                                                
                                                </select>
                                                <div class="fv-plugins-message-container"></div>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row fv-plugins-icon-container">
                                            <label class="col-xl-3 col-lg-3 col-form-label">Course Sub Category</label>
                                            <div class="col-lg-9 col-xl-9">
                                                <input class="form-control form-control-solid form-control-lg" name="name_cat" type="text" value="">
                                                <div class="fv-plugins-message-container"></div>
                                            </div>
                                        </div>
                                        <!--end::Group-->
                                        
                                        <div class="form-group">
                                        <label>File Browser</label>
                                        <div></div>
                                        <div class="custom-file">
                                            <input type="file" name="file" class="custom-file-input" id="customFile" />
                                            <label class="custom-file-label" for="customFile">Choose Video</label>
                                        </div>
                                        @error('file')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-3 col-form-label">Title</label>
                                        <div class="col-9">
                                            <input class="form-control" type="text" name="sub_title"  id="example-text-input">
                                            @error('sub_title')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                        </div>
                                    </div>

                                    <!--begin::Group-->
                                    <div class="form-group row fv-plugins-icon-container">
                                        <label class="col-xl-3 col-lg-3 col-form-label">Description</label>
                                        <div class="col-lg-9 col-xl-9">
                                            <!-- <div class="summernote"></div> -->

                                            <textarea name="txtVideoInfo"  id="txtVideoInfo" cols="5" class="form-control summernote" rows="10"></textarea>

                                            <div class="fv-plugins-message-container"></div>
                                            @error('txtVideoInfo')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                       
                                        <!--end::Group-->
                                        
                                       

                                    </div>
                                    <!--end::Wizard Step 1-->
                                  
                                 
                                 
                                    <!--begin::Wizard Actions-->
                                    <div class="d-flex justify-content-between border-top pt-10 mt-15">
                                       
                                        <div>
                                            <button type="submit" class="btn btn-success font-weight-bolder px-9 py-4" data-wizard-type="action-submit">Submit</button>
                                            <button type="button" id="next-step" class="btn btn-primary font-weight-bolder px-9 py-4" data-wizard-type="action-next">Reset</button>
                                        </div>
                                    </div>
                                    <!--end::Wizard Actions-->
                                </div>
                            </div>
                           
                        </form>
                        <!--end::Wizard Form-->
                    </div>
                </div>
            </div>



        </div>
    </div>
    <!--end::Card-->
</div>
<!--end::Container-->