<!--begin::Container-->
<div class="container">

    <!--begin::Card-->
    <div class="card card-custom">
        <div class="card-header">
            <div class="card-title">
                <span class="card-icon">
                    <i class="flaticon2-supermarket text-primary"></i>
                </span>
                <h3 class="card-label">Add Video and Description to Sub Category</h3>
            </div>

        </div>
        <div class="card-body">
            <div class="card-body p-0">
                <div class="row justify-content-center py-8 px-8 py-lg-15 px-lg-10">

                    <div class="col-xl-12 col-xxl-10">


                        @if (\Session::has('success'))
                        <div class="alert alert-success">
                            <ul>
                                <li>{!! \Session::get('success') !!}</li>
                            </ul>
                        </div>
                        @endif



                        <!--begin::Wizard Form-->
                        <form class="form fv-plugins-bootstrap fv-plugins-framework" method="post" action="{{route('uploadFile')}}" enctype="multipart/form-data">
                            <input type="hidden" id="txtAction" name="txtAction" value="__edit">
                            <input type="hidden" id="txtID" name="txtID" value="{{$data->id}}">
                            <input type="hidden" id="action" name="action" value="31">
                            <input type="hidden" name="txtSID" id="txtSID" value="{{ Request::segment(2) }}">
                            @csrf
                            <div class="row justify-content-center">
                                <div class="col-xl-9">
                                    <!--begin::Wizard Step 1-->
                                    <div class="form-group">
                                        <label>File Browser</label>
                                        <div></div>
                                        <div class="custom-file">
                                            <input type="file" name="file" class="custom-file-input" id="customFile" />
                                            <label class="custom-file-label" for="customFile">Choose file</label>
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

                                            <textarea name="txtVideoInfo" data-provide="markdown" id="txtVideoInfo" cols="5" class="form-control summernote" rows="10"></textarea>

                                            <div class="fv-plugins-message-container"></div>
                                            @error('txtVideoInfo')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>


                                    <!--end::Wizard Step 1-->

                                    <div class="d-flex justify-content-between border-top pt-10 mt-15">

                                        <div>
                                            <button type="submit" class="btn btn-success font-weight-bolder px-9 py-4" data-wizard-type="action-submit">Submit</button>
                                            <button type="button" id="next-step" class="btn btn-primary font-weight-bolder px-9 py-4" data-wizard-type="action-next">Reset</button>
                                        </div>
                                    </div>


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