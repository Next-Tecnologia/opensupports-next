import React from 'react';
import _ from 'lodash';
import {connect}  from 'react-redux';

import SearchFiltersActions from 'actions/search-filters-actions';

import i18n from 'lib-app/i18n';
import API from 'lib-app/api-call';
import history from 'lib-app/history';
import searchTicketsUtils from 'lib-app/search-tickets-utils';

import DateTransformer from 'lib-core/date-transformer';

import Form from 'core-components/form';
import SubmitButton from 'core-components/submit-button';
import FormField from 'core-components/form-field';
import Icon from 'core-components/icon';
import Button from 'core-components/button';
import Loading from 'core-components/loading';


const INITIAL_PAGE = 1;

const DEFAULT_START_DATE = 20170101;

class TicketQueryFilters extends React.Component {

    static propTypes = {
        filters: React.PropTypes.shape({
            query: React.PropTypes.string,
            departments: React.PropTypes.string,
            owners: React.PropTypes.string,
            tags: React.PropTypes.string,
            dateRange: React.PropTypes.string,
        })
    }

    render() {
        const {
            formState,
            filters,
            showFilters,
            ticketQueryListState
        } = this.props;

        return (
            <div className={"ticket-query-filters" + (showFilters ? "__open" : "") }>
                <Form
                    loading={ticketQueryListState.loading}
                    values={this.getFormValue(formState)}
                    onChange={this.onChangeForm.bind(this)}
                    onSubmit={this.onSubmitListConfig.bind(this)}>
                    <div className="ticket-query-filters__search-box">
                        <FormField name="query" field="search-box" fieldProps={{onSearch: this.onSubmitListConfig.bind(this)}} />
                    </div>
                    <div className="ticket-query-filters__row">
                        <div className="ticket-query-filters__row__filter">
                            <span>{i18n('DATE')}</span>
                            <FormField
                                name="dateRange"
                                field="date-range"
                                fieldProps={{defaultValue: this.dateRangeToFormValue(filters.dateRange)}} />
                        </div>
                        <div className="ticket-query-filters__row__filter">
                            <span>{i18n('STATUS')}</span>
                            <FormField name="closed" field="select" fieldProps={{items: this.getStatusItems()}} />
                        </div>
                    </div>
                    <div className="ticket-query-filters__row">
                        <div className="ticket-query-filters__row__filter">
                            <span className="ticket-query-filters__title">{i18n('DEPARTMENTS')}</span>
                            <FormField
                                name="departments"
                                field="autocomplete"
                                fieldProps={{items: this.getDepartmentsItems()}} />
                        </div>
                        <div className="ticket-query-filters__row__filter">
                            <span className="ticket-query-filters__title">{i18n('OWNER')}</span>
                            <FormField
                                name="owners"
                                field="autocomplete"
                                fieldProps={{items: this.getStaffList()}} />
                        </div>
                    </div>
                    <div className="ticket-query-filters__row">
                        <div className="ticket-query-filters__row__filter">
                            <span className="ticket-query-filters__title">{i18n('TAGS')}</span>
                            <FormField
                                name="tags"
                                field="tag-selector"
                                fieldProps={{
                                    items: this.getTags(filters.tags),
                                    onRemoveClick: this.removeTag.bind(this),
                                    onTagSelected: this.addTag.bind(this)
                                }} />
                        </div>
                        <div className="ticket-query-filters__row__filter">
                            <span className="ticket-query-filters__title">{i18n('AUTHORS')}</span>
                            <FormField
                                name="authors"
                                field="autocomplete"
                                fieldProps={{
                                    getItemListFromQuery: this.searchAuthors.bind(this),
                                    comparerFunction: this.autorsComparer.bind(this)
                                }} />
                        </div>
                    </div>
                    <div className="ticket-query-filters__container">
                        <Button
                            className="ticket-query-filters__container__button ticket-query-filters__container__clear-button"
                            size= "medium"
                            disabled={ticketQueryListState.loading}
                            onClick={this.clearFormValues.bind(this)}>
                                {ticketQueryListState.loading ?
                                    <Loading />
                                    : i18n('CLEAR')}
                        </Button>
                        <SubmitButton
                            className="ticket-query-filters__container__button ticket-query-filters__container__search-button"
                            type="secondary"
                            size= "medium">
                                {i18n('SEARCH')}
                        </SubmitButton>
                    </div>
                </Form>
                <span className="separator" />
            </div>
        );
    }

    searchAuthors(query, blacklist = []) {
        return API.call({
            path: '/ticket/search-authors',
            data: {
                query: query,
                blackList: JSON.stringify(blacklist)
            }
        }).then(r => {
            return r.data.authors.map(author => {
                return {
                    name: author.name,
                    color: "gray",
                    id: author.id*1,
                    profilePic: author.profilePic,
                    isStaff: author.isStaff * 1,
                    content: author.profilePic !== undefined ? this.renderStaffOption(author) : author.name,
                    contentOnSelected: author.profilePic !== undefined ? this.renderStaffSelected(author) : author.name
                }});
        });
    }

    renderDepartmentOption(department) {
        return (
            <div className="ticket-query-filters__department-option" key={`department-option-${department.id}`}>
                {department.private*1 ?
                    <Icon className="ticket-query-filters__department-option__icon" name='user-secret'/> :
                    null}
                <span className="ticket-query-filters__department-option__name">{department.name}</span>
            </div>
        );
    }

    renderDeparmentSelected(department) {
        return (
            <div className="ticket-query-filters__department-selected" key={`department-selected-${department.id}`}>
                {department.private*1 ?
                    <Icon className="ticket-query-filters__department-selected__icon" name='user-secret'/> :
                    null}
                <span className="ticket-query-filters__department-selected__name">{department.name}</span>
            </div>
        );
    }

    renderStaffOption(staff) {
        return (
            <div className="ticket-query-filters__staff-option" key={`staff-option-${staff.id}`}>
                <img className="ticket-query-filters__staff-option__profile-pic" src={this.getStaffProfilePic(staff)}/>
                <span className="ticket-query-filters__staff-option__name">{staff.name}</span>
            </div>
        );
    }

    renderStaffSelected(staff) {
        return (
            <div className="ticket-query-filters__staff-selected" key={`staff-selected-${staff.id}`}>
                <img className="ticket-query-filters__staff-selected__profile-pic" src={this.getStaffProfilePic(staff)}/>
                <span className="ticket-query-filters__staff-selected__name">{staff.name}</span>
            </div>
        );
    }

    addTag(tag) {
        const { formState } = this.props;
        let selectedTagsId = formState.tags.concat(this.tagsNametoTagsId(this.getSelectedTagsName([tag])));

        this.onChangeFormState({...formState, tags: selectedTagsId});
    }

    autorsComparer(autorList, autorSelectedList) {
        return autorList.filter(item  => !_.some(autorSelectedList, {id: item.id, isStaff: item.isStaff}));
    }

    clearFormValues(event) {
        event.preventDefault();
        this.props.dispatch(SearchFiltersActions.setDefaultFormValues());
    }

    dateRangeToFormValue(_dateRange) {
        const dateRange = JSON.parse(_dateRange);

        return {
            valid: true,
            startDate: dateRange[0]/10000,
            endDate: (dateRange[1]-2400)/10000,
        };
    }

    getDepartmentsItems() {
        const { departments, } = this.props;
        let departmentsList = departments.map(department => {
            return {
                id: JSON.parse(department.id),
                name: department.name.toLowerCase(),
                color: 'gray',
                contentOnSelected: this.renderDeparmentSelected(department),
                content: this.renderDepartmentOption(department),
            }
        });

        return departmentsList;
    }

    getSelectedDepartments(selectedDepartmentsId) {
        let selectedDepartments = [];

        if(selectedDepartmentsId !== undefined) {
            let departments = this.getDepartmentsItems();
            selectedDepartments = departments.filter(item => _.includes(selectedDepartmentsId, item.id));
        }

        return selectedDepartments;
    }

    getSelectedStaffs(selectedStaffsId) {
        let selectedStaffs = [];
        if(selectedStaffsId !== undefined) {
            let staffs = this.getStaffList();
            selectedStaffs = staffs.filter(staff => _.includes(selectedStaffsId, staff.id));
        }

        return selectedStaffs;
    }

    getSelectedTagsName(selectedTagsId) {
        let selectedTagsName = [];

        if(selectedTagsId !== undefined) {
            let tagList = this.getTags();
            let selectedTags = tagList.filter(item => _.includes(selectedTagsId, item.id));
            selectedTagsName = selectedTags.map(tag => tag.name);
        }

        return selectedTagsName;
    }

    getStaffList() {
        const { staffList, } = this.props;
        let newStaffList = staffList.map(staff => {
            return {
                id: JSON.parse(staff.id),
                name: staff.name.toLowerCase(),
                color: 'gray',
                contentOnSelected: this.renderStaffSelected(staff),
                content: this.renderStaffOption(staff),
            }
        });

        return newStaffList;
    }

    getStaffProfilePic(staff) {
        return staff.profilePic ? API.getFileLink(staff.profilePic) : (API.getURL() + '/images/profile.png');
    }

    getStatusItems() {
        let items = [
            {id: 0, name: 'Any', content: i18n('ANY')},
            {id: 1, name: 'Opened', content: i18n('OPENED')},
            {id: 2, name: 'Closed', content: i18n('CLOSED')},
        ];

        return items;
    }

    getTags() {
        const { tags, } = this.props;
        let newTagList = tags.map(tag => {
            return {
                id: JSON.parse(tag.id),
                name: tag.name,
                color : tag.color
            }
        });

        return newTagList;
    }

    onChangeFormState(formValues) {
        this.props.dispatch(SearchFiltersActions.changeForm(formValues));
    }

    onSubmitListConfig() {
        const {
            formState,
            filters,
            formEdited,
        } = this.props;
        const listConfigWithCompleteAuthorsList = searchTicketsUtils.formValueToListConfig(
            {...formState, orderBy: filters.orderBy, page: 1},
            true
        );
        if(formEdited) {
            const filtersForAPI = searchTicketsUtils.prepareFiltersForAPI(listConfigWithCompleteAuthorsList.filters);
            const currentPath = window.location.pathname;
            const urlQuery = searchTicketsUtils.getFiltersForURL({
                filters: filtersForAPI,
                shouldRemoveCustomParam: true,
                shouldRemoveUseInitialValuesParam: true
            });
            urlQuery && history.push(`${currentPath}${urlQuery}`);
        }
    }

    removeTag(tag) {
        const { formState } = this.props;
        let tagListName = formState.tags;
        let newTagList = tagListName.filter(item => item !== tag);
        let selectedTags = this.tagsNametoTagsId(this.getSelectedTagsName(newTagList));

        this.onChangeFormState({...formState, tags: selectedTags});
    }

    tagsNametoTagsId(selectedTagsName) {
        let tagList = this.getTags();
        let selectedTags = tagList.filter(item => _.includes(selectedTagsName, item.name));
        let selectedTagsId = selectedTags.map(tag => tag.id);

        return selectedTagsId;
    }

    onChangeForm(data) {
      let newStartDate = data.dateRange.startDate === "" ? DEFAULT_START_DATE : data.dateRange.startDate;
      let newEndDate = data.dateRange.endDate === "" ? DateTransformer.getDateToday() : data.dateRange.endDate;
      let departmentsId = data.departments.map(department => department.id);
      let staffsId = data.owners.map(staff => staff.id);
      let tagsName = this.tagsNametoTagsId(data.tags);
      let authors = data.authors.map(({name, id, isStaff, profilePic, color}) => ({name, id: id*1, isStaff, profilePic, color}));

      this.onChangeFormState({
          ...data,
          tags: tagsName,
          owners: staffsId,
          departments: departmentsId,
          authors: authors,
          dateRange: {
              ...data.dateRange,
              startDate: newStartDate,
              endDate: newEndDate
          }
      });
    }

    getFormValue(form) {
        return {
            ...form,
            departments: this.getSelectedDepartments(form.departments),
            owners: this.getSelectedStaffs(form.owners),
            tags: this.getSelectedTagsName(form.tags),
            authors: this.getAuthors(form.authors),
        }
    }

    getAuthors(authors = []) {
        return authors.map(author => ({
            name: author.name,
            color: "gray",
            id: author.id*1,
            isStaff: author.isStaff*1,
            profilePic: author.profilePic,
            content: author.profilePic !== undefined ? this.renderStaffOption(author) : author.name,
            contentOnSelected: author.profilePic !== undefined ? this.renderStaffSelected(author) : author.name
        }));
    }

}

export default connect((store) => {
    return {
        tags: store.config.tags,
        departments: store.session.userDepartments,
        staffList: store.adminData.staffMembers,
        formState: store.searchFilters.form,
        filters: store.searchFilters.listConfig.filters,
        showFilters: store.searchFilters.showFilters,
        formEdited: store.searchFilters.formEdited,
        ticketQueryListState: store.searchFilters.ticketQueryListState,
    };
})(TicketQueryFilters);
