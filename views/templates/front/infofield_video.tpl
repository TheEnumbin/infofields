<div class="video-container">
    {if isset($infofield.img_height) && isset($infofield.img_width)}
        {assign var="vid_height" value=$infofield.img_height}
        {assign var="vid_width" value=$infofield.img_width}
    {else}
        {assign var="vid_height" value="315"}
        {assign var="vid_width" value="560"}
    {/if}
    {assign var="vid_data" value=$infometa}
    <iframe width="{$vid_width}" height="{$vid_height}"
        src="https://www.youtube.com/embed/{$vid_data|replace:'https://www.youtube.com/watch?v=':''}"
        title="YouTube video player" frameborder="0"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
        allowfullscreen>
    </iframe>
</div>