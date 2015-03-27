<div class="ui basic segment" style="width: 400px;margin: 0 auto">
    <div class="ui attached message">
        <div class="header">
            教育資料庫資料查詢平台
        </div>
        <p>請輸入您註冊的電子郵件信箱，系統將會發送一封重新設定您的密碼的信件到這個信箱。</p>
    </div>
    
    @include('demo.remind')
    
    <div class="ui bottom attached warning message">
        <i class="icon help"></i>
        Already signed up? <a href="#">Login here</a> instead.
    </div>
</div>