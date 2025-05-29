<article class="single-entry-3 rounded">
    <div class="entry-thumb position-relative">
        <a href="{{ $url }}">
            <img src="{{ $image }}" style="width:100%;max-height:200px;">
        </a>
        <div class="date-meta text-white">
            <span> {{ $date }}</span> {{ $month }}
        </div>
    </div>
    <div class="entry-txt p-3">
        <h3>
            <a href="{{ $url }}">{{ $title }}</a>
        </h3>
        <div class="clearfix pt-3 border-top">
            <div class="admin-meta d-flex float-start">
                {{ $timeAgo }}
            </div>
            <div class="admin-meta d-flex float-end">
                Dilihat : <b style="color:orange;">&nbsp; {{ $views }} </b>&nbsp; Users
            </div>
            <br>
            <span style="font-size:12px;">Upload By : <b>{{ $uploader }}</b></span>
        </div>
    </div>
</article>
