<nav class="main-header navbar navbar-expand navbar-white navbar-light">

    <!-- Left navbar links -->
    <ul class="navbar-nav sammmu">
       <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
       </li>
      <!--  <li class="nav-item d-none d-sm-inline-block">
          <a href="index3.html" class="nav-link">Home</a>
       </li> -->
       <li class="nav-item d-none d-sm-inline-block"><a class="nav-link" href="{{ route('adminarea.home') }}"><i class="fa fa-home"></i> {{ trans('cortex/foundation::common.home') }}</a></li>
       <!-- <li class="nav-item d-none d-sm-inline-block">
          <a href="#" class="nav-link">Contact</a>
       </li> -->
    </ul>
    <div class="navbar-custom-menu navbar-nav ml-auto ">
    {!! Menu::render('adminarea.header.language') !!}
    {!! Menu::render('adminarea.header.user') !!}
    </div>  
 </nav>