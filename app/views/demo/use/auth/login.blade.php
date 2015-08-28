
        <!-- <div class="row" style="height:100px"></div> -->

        <div class="six wide column">

            <div class="ui segment">
                @include('demo.use.auth.news')
            </div>
            
        </div>

        <div class="six wide column">
			
            <h3 class="ui top attached center aligned header">臺灣後期中等教育整合資料庫查詢平台</h3>

			<div class="ui attached segment">
				@include('demo.auth.login')
			</div>

            <div class="ui bottom attached warning message">
                <i class="icon help"></i>
                <?=link_to('project/' . Request::segment(2) . '/password/remind', '忘記密碼')?>
                <br />
                <i class="icon help"></i>
                <?=link_to('project/'. Request::segment(2) . '/register/help', '需要幫助嗎', ['target' => '_blank'])?>
                <br />
                <i class="icon file"></i>
                <a class="item" target="_blank" href="https://plat.cher.ntnu.edu.tw/files/CERE-ISMS-D-031_%E6%9F%A5%E8%A9%A2%E5%B9%B3%E5%8F%B0%E5%B8%B3%E8%99%9F%E4%BD%BF%E7%94%A8%E6%AC%8A%E7%94%B3%E8%AB%8B%E3%80%81%E8%AE%8A%E6%9B%B4%E3%80%81%E8%A8%BB%E9%8A%B7%E8%A1%A8_v2.1(1030703%E4%BF%AE%E5%AE%9A).doc" />帳號修改、註銷表</a>
            </div>

        </div>