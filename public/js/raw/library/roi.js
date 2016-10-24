/**
 * Created by Daniel Dimmick on 25/03/15.
 */
function RoiMyEventsReport(ajaxType){
    this.data=null;
    this.orderBy='startDateTime';
    this.order='desc';

    this.resultSet=null;

    this.getResultSet=function(){
        return this.resultSet;
    }

    this.setResultSet=function(resultSet){
        this.resultSet=resultSet;
    }

    this.setOrderBy=function(order){
        this.orderBy=order;
    }
    this.setOrder=function(order){
        this.order=order;
    }
    this.setDate=function(data){
        this.data=data;
    }

    this.getData=function(){
        return this.data;
    }
    this.getOrderBy=function(){
        return this.orderBy;
    }
    this.getOrder=function(){
        return this.order;
    }

    this.ajax= function (){
        return $.ajax({
            type: 'POST',
            url: '/roi/'+ajaxType,
            dataType: 'json',
            data: this.getData()
        });
    }
    this.fetch= function(){
        return this.ajax();
    }
}

function RoiMyPeopleReport(ajaxType){
    this.data=null;
    this.orderBy='userId';
    this.order='desc';

    this.resultSet=null;

    this.getResultSet=function(){
        return this.resultSet;
    }

    this.setResultSet=function(resultSet){
        this.resultSet=resultSet;
    }

    this.setOrderBy=function(order){
        this.orderBy=order;
    }
    this.setOrder=function(order){
        this.order=order;
    }
    this.setDate=function(data){
        this.data=data;
    }

    this.getData=function(){
        return this.data;
    }
    this.getOrderBy=function(){
        return this.orderBy;
    }
    this.getOrder=function(){
        return this.order;
    }

    this.ajax= function (){
        return $.ajax({
            type: 'POST',
            url: '/roi/'+ajaxType,
            dataType: 'json',
            data: this.getData()
        });
    }
    this.fetch= function(){
        return this.ajax();
    }
}